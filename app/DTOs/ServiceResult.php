<?php

namespace App\DTOs;

class ServiceResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public mixed $data = null,
        public int $statusCode = 200,
        public ?array $errors = null
    ) {}

    public static function success(mixed $data = null, string $message = 'Success', int $statusCode = 200): self
    {
        return new self(true, $message, $data, $statusCode, null);
    }

    public static function error(string $message = 'Error', int $statusCode = 400, ?array $errors = null): self
    {
        return new self(false, $message, null, $statusCode, $errors);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
}
