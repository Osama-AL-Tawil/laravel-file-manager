<?php

namespace OST\LaravelFileManager\Models;

class UploadResponse
{

    private bool $status;
    private string|null $message;
    private array|null $file_paths;
    public function __construct(bool $status,string|null $message,array|null $file_paths)
    {
        $this->status = $status;
        $this->message = $message;
        $this->file_paths = $file_paths;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getFilePaths(): array
    {
        return $this->file_paths;
    }

}
