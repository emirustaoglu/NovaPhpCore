<?php

namespace NovaCore\Mail\Queue;

interface QueueDriverInterface
{
    public function push(QueuedMail $mail): bool;
    public function pop(): ?QueuedMail;
    public function delete(string $id): bool;
    public function clear(): bool;
    public function count(): int;
    public function failed(QueuedMail $mail, string $error): bool;
    public function retry(string $id): bool;
}
