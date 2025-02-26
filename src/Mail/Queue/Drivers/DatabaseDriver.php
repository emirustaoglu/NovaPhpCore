<?php

namespace NovaCore\Mail\Queue\Drivers;

use NovaCore\Database\DB;
use NovaCore\Mail\Queue\QueueDriverInterface;
use NovaCore\Mail\Queue\QueuedMail;

class DatabaseDriver implements QueueDriverInterface
{
    protected array $config;
    protected string $table;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->table = $config['table'] ?? 'mail_queue';
        $this->ensureTableExists();
    }

    public function push(QueuedMail $mail): bool
    {
        return DB::table($this->table)->insert([
            'id' => $mail->getId(),
            'queue' => $this->config['queue'] ?? 'default',
            'payload' => serialize($mail),
            'attempts' => 0,
            'scheduled_for' => $mail->getScheduledFor(),
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'pending'
        ]);
    }

    public function pop(): ?QueuedMail
    {
        $record = DB::table($this->table)
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('scheduled_for')
                    ->orWhere('scheduled_for', '<=', date('Y-m-d H:i:s'));
            })
            ->orderBy('created_at')
            ->first();

        if (!$record) {
            return null;
        }

        // Update status to processing
        DB::table($this->table)
            ->where('id', $record->id)
            ->update([
                'status' => 'processing',
                'attempts' => DB::raw('attempts + 1'),
                'last_attempt' => date('Y-m-d H:i:s')
            ]);

        return unserialize($record->payload);
    }

    public function delete(string $id): bool
    {
        return DB::table($this->table)->where('id', $id)->delete() > 0;
    }

    public function clear(): bool
    {
        return DB::table($this->table)->truncate();
    }

    public function count(): int
    {
        return DB::table($this->table)->where('status', 'pending')->count();
    }

    public function failed(QueuedMail $mail, string $error): bool
    {
        return DB::table($this->table)
            ->where('id', $mail->getId())
            ->update([
                'status' => 'failed',
                'error' => $error,
                'failed_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function retry(string $id): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update([
                'status' => 'pending',
                'error' => null,
                'failed_at' => null,
                'attempts' => 0
            ]);
    }

    protected function ensureTableExists(): void
    {
        if (!DB::schema()->hasTable($this->table)) {
            DB::schema()->create($this->table, function ($table) {
                $table->string('id')->primary();
                $table->string('queue')->index();
                $table->longText('payload');
                $table->integer('attempts')->default(0);
                $table->string('status', 20)->index();
                $table->text('error')->nullable();
                $table->timestamp('scheduled_for')->nullable()->index();
                $table->timestamp('last_attempt')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }
    }
}
