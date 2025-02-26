<?php

namespace NovaCore\Upload\Contracts;

interface StorageInterface
{
    public function put(string $path, $content): bool;
    public function delete(string $path): bool;
    public function exists(string $path): bool;
    public function size(string $path): int;
    public function makeDirectory(string $path): bool;
}
