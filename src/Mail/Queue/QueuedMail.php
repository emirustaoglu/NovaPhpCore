<?php

namespace NovaCore\Mail\Queue;

class QueuedMail
{
    private string $id;
    private array $to = [];
    private array $cc = [];
    private array $bcc = [];
    private string $subject;
    private string $body;
    private ?string $altBody;
    private array $attachments;
    private bool $isHtml;
    private int $attempts = 0;
    private ?string $failedReason = null;
    private ?string $scheduledFor = null;
    private string $status = 'pending'; // pending, processing, completed, failed
    private ?string $trackingId = null;

    public function __construct(array $data)
    {
        $this->id = uniqid('mail_', true);
        $this->to = $data['to'] ?? [];
        $this->cc = $data['cc'] ?? [];
        $this->bcc = $data['bcc'] ?? [];
        $this->subject = $data['subject'];
        $this->body = $data['body'];
        $this->altBody = $data['altBody'] ?? null;
        $this->attachments = $data['attachments'] ?? [];
        $this->isHtml = $data['isHtml'] ?? true;
        $this->scheduledFor = $data['scheduledFor'] ?? null;
        $this->trackingId = $data['trackingId'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'to' => $this->to,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'subject' => $this->subject,
            'body' => $this->body,
            'altBody' => $this->altBody,
            'attachments' => $this->attachments,
            'isHtml' => $this->isHtml,
            'attempts' => $this->attempts,
            'failedReason' => $this->failedReason,
            'scheduledFor' => $this->scheduledFor,
            'status' => $this->status,
            'trackingId' => $this->trackingId
        ];
    }

    public function incrementAttempts(): void
    {
        $this->attempts++;
    }

    public function markAsFailed(string $reason): void
    {
        $this->status = 'failed';
        $this->failedReason = $reason;
    }

    public function markAsCompleted(): void
    {
        $this->status = 'completed';
    }

    public function markAsProcessing(): void
    {
        $this->status = 'processing';
    }

    // Getters
    public function getId(): string
    {
        return $this->id;
    }

    public function getTo(): array
    {
        return $this->to;
    }

    public function getCc(): array
    {
        return $this->cc;
    }

    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getAltBody(): ?string
    {
        return $this->altBody;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function isHtml(): bool
    {
        return $this->isHtml;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function getFailedReason(): ?string
    {
        return $this->failedReason;
    }

    public function getScheduledFor(): ?string
    {
        return $this->scheduledFor;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTrackingId(): ?string
    {
        return $this->trackingId;
    }
}
