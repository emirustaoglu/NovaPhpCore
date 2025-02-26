<?php

namespace NovaCore\Mail;

use NovaCore\Config\ConfigLoader;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    private PHPMailer $mailer;
    private array $config;
    private array $attachments = [];
    private array $to = [];
    private array $cc = [];
    private array $bcc = [];
    private ?string $subject = null;
    private ?string $body = null;
    private ?string $altBody = null;
    private bool $isHtml = true;
    private ?string $view = null;
    private array $viewData = [];

    public function __construct()
    {
        $this->config = ConfigLoader::getInstance()->get('mail');
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure(): void
    {
        try {
            // Server settings
            $this->mailer->SMTPDebug = $this->config['debug'] ?? SMTP::DEBUG_OFF;
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['username'];
            $this->mailer->Password = $this->config['password'];
            $this->mailer->SMTPSecure = $this->config['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $this->config['port'] ?? 587;
            $this->mailer->CharSet = $this->config['charset'] ?? 'UTF-8';

            // Default sender
            if (isset($this->config['from'])) {
                $this->mailer->setFrom(
                    $this->config['from']['address'],
                    $this->config['from']['name'] ?? ''
                );
            }
        } catch (Exception $e) {
            throw new MailException("Mail configuration error: {$e->getMessage()}");
        }
    }

    public function to(string|array $address, string $name = ''): self
    {
        if (is_array($address)) {
            foreach ($address as $email => $recipientName) {
                $this->to[] = [
                    'address' => is_numeric($email) ? $recipientName : $email,
                    'name' => is_numeric($email) ? '' : $recipientName
                ];
            }
        } else {
            $this->to[] = ['address' => $address, 'name' => $name];
        }
        return $this;
    }

    public function cc(string|array $address, string $name = ''): self
    {
        if (is_array($address)) {
            foreach ($address as $email => $recipientName) {
                $this->cc[] = [
                    'address' => is_numeric($email) ? $recipientName : $email,
                    'name' => is_numeric($email) ? '' : $recipientName
                ];
            }
        } else {
            $this->cc[] = ['address' => $address, 'name' => $name];
        }
        return $this;
    }

    public function bcc(string|array $address, string $name = ''): self
    {
        if (is_array($address)) {
            foreach ($address as $email => $recipientName) {
                $this->bcc[] = [
                    'address' => is_numeric($email) ? $recipientName : $email,
                    'name' => is_numeric($email) ? '' : $recipientName
                ];
            }
        } else {
            $this->bcc[] = ['address' => $address, 'name' => $name];
        }
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function body(string $body, bool $isHtml = true): self
    {
        $this->body = $body;
        $this->isHtml = $isHtml;
        return $this;
    }

    public function view(string $view, array $data = []): self
    {
        $this->view = $view;
        $this->viewData = $data;
        $this->isHtml = true;
        return $this;
    }

    public function altBody(string $altBody): self
    {
        $this->altBody = $altBody;
        return $this;
    }

    public function attach(string $path, string $name = ''): self
    {
        $this->attachments[] = [
            'path' => $path,
            'name' => $name
        ];
        return $this;
    }

    public function send(): bool
    {
        try {
            // Recipients
            foreach ($this->to as $recipient) {
                $this->mailer->addAddress($recipient['address'], $recipient['name']);
            }

            foreach ($this->cc as $recipient) {
                $this->mailer->addCC($recipient['address'], $recipient['name']);
            }

            foreach ($this->bcc as $recipient) {
                $this->mailer->addBCC($recipient['address'], $recipient['name']);
            }

            // Attachments
            foreach ($this->attachments as $attachment) {
                $this->mailer->addAttachment($attachment['path'], $attachment['name']);
            }

            // Content
            $this->mailer->isHTML($this->isHtml);
            $this->mailer->Subject = $this->subject;

            // If view is set, render it
            if ($this->view) {
                $this->body = view($this->view, $this->viewData)->render();
            }
            
            $this->mailer->Body = $this->body;
            
            if ($this->altBody) {
                $this->mailer->AltBody = $this->altBody;
            }

            return $this->mailer->send();
        } catch (Exception $e) {
            throw new MailException("Mail sending error: {$e->getMessage()}");
        }
    }

    public function queue(): bool
    {
        $queueData = [
            'to' => $this->to,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'subject' => $this->subject,
            'body' => $this->body,
            'altBody' => $this->altBody,
            'attachments' => $this->attachments,
            'isHtml' => $this->isHtml,
            'view' => $this->view,
            'viewData' => $this->viewData
        ];

        $queuedMail = new Queue\QueuedMail($queueData);
        $mailQueue = new Queue\MailQueue($this->config);
        
        return $mailQueue->push($queuedMail);
    }

    public function later(\DateTime $dateTime): bool
    {
        $queueData = [
            'to' => $this->to,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'subject' => $this->subject,
            'body' => $this->body,
            'altBody' => $this->altBody,
            'attachments' => $this->attachments,
            'isHtml' => $this->isHtml,
            'view' => $this->view,
            'viewData' => $this->viewData,
            'scheduledFor' => $dateTime->format('Y-m-d H:i:s')
        ];

        $queuedMail = new Queue\QueuedMail($queueData);
        $mailQueue = new Queue\MailQueue($this->config);
        
        return $mailQueue->push($queuedMail);
    }
}
