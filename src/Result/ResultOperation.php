<?php

namespace App\Result;

class ResultOperation
{
    private bool $success;
    private string $message;

    /**
     * Summary of data
     * @var array T[]
     */
    private array $data;

    public function __construct(bool $success, string $message = '', array $data = [])
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function clearData()
    {
        $this->data = [];
    }

    public static function createSuccess(string $message = '', array $data = []): self
    {
        return new self(true, $message, $data);
    }

    public static function createFailure(string $message): self
    {
        return new self(false, $message);
    }
}
