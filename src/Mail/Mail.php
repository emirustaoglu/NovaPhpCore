<?php

namespace NovaPhp\Core\Mail;

use Jenssegers\Blade\Blade;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class Mail
{
    private Mailer $mailer;
    private Email $email;
    private array $config;
    private ?string $view = null;
    private array $viewData = [];

    public function __construct()
    {
        $this->config = config('mail');
        $default = $this->config['default'];
        $dsn = sprintf(
            'smtp://%s:%s@%s:%d?encryption=%s&timeout=%d',
            $this->config['mailers'][$default]['username'],
            $this->config['mailers'][$default]['password'],
            $this->config['mailers'][$default]['host'],
            $this->config['mailers'][$default]['port'],
            $this->config['mailers'][$default]['encryption'],
            $this->config['mailers'][$default]['timeout'] ?? 30
        );

        $transport = Transport::fromDsn($dsn);
        $this->mailer = new Mailer($transport);
        $this->email = new Email();
        $this->email->from($this->config['from']['address']);
    }

    public function to(string|array $address): self
    {
        $this->email->to(...(array)$address);
        return $this;
    }

    public function cc(string|array $address): self
    {
        $this->email->cc(...(array)$address);
        return $this;
    }

    public function bcc(string|array $address): self
    {
        $this->email->bcc(...(array)$address);
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->email->subject($subject);
        return $this;
    }

    public function body(string $body, bool $isHtml = true): self
    {
        if ($isHtml) {
            $this->email->html($body);
        } else {
            $this->email->text($body);
        }
        return $this;
    }

    public function view(string $view, array $data = []): self
    {
        $this->view = $view;
        $this->viewData = $data;
        return $this;
    }

    public function attach(string $path, string $name = ''): self
    {
        $this->email->attachFromPath($path, $name);
        return $this;
    }

    public function send(): void
    {
        $blade = new Blade(config('app.paths.view'), config('app.paths.views_cache'));
        if ($this->view) {
            $this->body($blade->render($this->view, $this->viewData));
        }

        $this->mailer->send($this->email);
    }
}
