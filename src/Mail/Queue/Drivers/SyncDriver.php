<?php

namespace NovaCore\Mail\Queue\Drivers;

use NovaCore\Mail\Mail;
use NovaCore\Mail\Queue\QueueDriverInterface;
use NovaCore\Mail\Queue\QueuedMail;

class SyncDriver implements QueueDriverInterface
{
    public function push(QueuedMail $mail): bool
    {
        // Senkron sürücüde mail hemen gönderilir
        $mailer = new Mail();
        
        foreach ($mail->getTo() as $recipient) {
            $mailer->to($recipient['address'], $recipient['name']);
        }

        foreach ($mail->getCc() as $recipient) {
            $mailer->cc($recipient['address'], $recipient['name']);
        }

        foreach ($mail->getBcc() as $recipient) {
            $mailer->bcc($recipient['address'], $recipient['name']);
        }

        foreach ($mail->getAttachments() as $attachment) {
            $mailer->attach($attachment['path'], $attachment['name']);
        }

        $mailer->subject($mail->getSubject())
               ->body($mail->getBody(), $mail->isHtml());

        if ($mail->getAltBody()) {
            $mailer->altBody($mail->getAltBody());
        }

        return $mailer->send();
    }

    public function pop(): ?QueuedMail
    {
        return null; // Sync driver doesn't store mails
    }

    public function delete(string $id): bool
    {
        return true; // Nothing to delete in sync driver
    }

    public function clear(): bool
    {
        return true; // Nothing to clear in sync driver
    }

    public function count(): int
    {
        return 0; // Sync driver doesn't store mails
    }

    public function failed(QueuedMail $mail, string $error): bool
    {
        return false; // Sync driver doesn't track failures
    }

    public function retry(string $id): bool
    {
        return false; // Sync driver doesn't support retries
    }
}
