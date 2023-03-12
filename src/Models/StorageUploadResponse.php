<?php

namespace OST\LaravelFileManager\Models;

class StorageUploadResponse
{
    private string $file_name;
    private string $file_path;

    /**
     * @param string $file_name
     * @param string $file_path
     */
    public function __construct(string $file_name, string $file_path)
    {
        $this->file_name = $file_name;
        $this->file_path = $file_path;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->file_name;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->file_path;
    }


}
