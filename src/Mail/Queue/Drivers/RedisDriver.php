<?php

namespace NovaCore\Mail\Queue\Drivers;

use NovaCore\Mail\Queue\QueueDriverInterface;
use NovaCore\Mail\Queue\QueuedMail;
use Redis;

class RedisDriver implements QueueDriverInterface
{
    protected Redis $redis;
    protected array $config;
    protected string $prefix;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->prefix = $config['prefix'] ?? 'mail_queue:';
        
        $this->redis = new Redis();
        $this->redis->connect(
            $config['host'] ?? '127.0.0.1',
            $config['port'] ?? 6379
        );

        if (isset($config['password'])) {
            $this->redis->auth($config['password']);
        }

        if (isset($config['database'])) {
            $this->redis->select($config['database']);
        }
    }

    public function push(QueuedMail $mail): bool
    {
        $id = $mail->getId();
        $queue = $this->config['queue'] ?? 'default';
        
        // Store mail data
        $this->redis->hSet(
            $this->prefix . 'mails',
            $id,
            serialize([
                'mail' => $mail,
                'attempts' => 0,
                'status' => 'pending',
                'created_at' => time()
            ])
        );

        // Add to queue
        if ($mail->getScheduledFor()) {
            $score = strtotime($mail->getScheduledFor());
            $this->redis->zAdd($this->prefix . 'scheduled', $score, $id);
        } else {
            $this->redis->lPush($this->prefix . 'queues:' . $queue, $id);
        }

        return true;
    }

    public function pop(): ?QueuedMail
    {
        $queue = $this->config['queue'] ?? 'default';

        // First check scheduled items
        $now = time();
        $items = $this->redis->zRangeByScore($this->prefix . 'scheduled', '-inf', $now, ['limit' => [0, 1]]);
        
        if (!empty($items)) {
            $id = $items[0];
            $this->redis->zRem($this->prefix . 'scheduled', $id);
        } else {
            $id = $this->redis->rPop($this->prefix . 'queues:' . $queue);
        }

        if (!$id) {
            return null;
        }

        $data = unserialize($this->redis->hGet($this->prefix . 'mails', $id));
        if (!$data) {
            return null;
        }

        // Update attempts and status
        $data['attempts']++;
        $data['status'] = 'processing';
        $data['last_attempt'] = time();
        
        $this->redis->hSet($this->prefix . 'mails', $id, serialize($data));

        return $data['mail'];
    }

    public function delete(string $id): bool
    {
        return $this->redis->hDel($this->prefix . 'mails', $id) > 0;
    }

    public function clear(): bool
    {
        $queue = $this->config['queue'] ?? 'default';
        $this->redis->del($this->prefix . 'mails');
        $this->redis->del($this->prefix . 'queues:' . $queue);
        $this->redis->del($this->prefix . 'scheduled');
        return true;
    }

    public function count(): int
    {
        $queue = $this->config['queue'] ?? 'default';
        return $this->redis->lLen($this->prefix . 'queues:' . $queue) +
               $this->redis->zCard($this->prefix . 'scheduled');
    }

    public function failed(QueuedMail $mail, string $error): bool
    {
        $id = $mail->getId();
        $data = unserialize($this->redis->hGet($this->prefix . 'mails', $id));
        
        if (!$data) {
            return false;
        }

        $data['status'] = 'failed';
        $data['error'] = $error;
        $data['failed_at'] = time();

        $this->redis->hSet($this->prefix . 'mails', $id, serialize($data));
        $this->redis->sAdd($this->prefix . 'failed', $id);

        return true;
    }

    public function retry(string $id): bool
    {
        $data = unserialize($this->redis->hGet($this->prefix . 'mails', $id));
        
        if (!$data) {
            return false;
        }

        $data['status'] = 'pending';
        $data['attempts'] = 0;
        $data['error'] = null;
        $data['failed_at'] = null;

        $this->redis->hSet($this->prefix . 'mails', $id, serialize($data));
        $this->redis->sRem($this->prefix . 'failed', $id);

        $queue = $this->config['queue'] ?? 'default';
        $this->redis->lPush($this->prefix . 'queues:' . $queue, $id);

        return true;
    }
}
