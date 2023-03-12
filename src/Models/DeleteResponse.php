<?php

namespace OST\LaravelFileManager\Models;

class DeleteResponse
{
    private bool $deleted_from_storage;
    private bool $deleted_from_db;

    /**
     * @param bool $deleted_from_storage
     * @param bool $deleted_from_db
     */
    public function __construct(bool $deleted_from_storage, bool $deleted_from_db)
    {
        $this->deleted_from_storage = $deleted_from_storage;
        $this->deleted_from_db = $deleted_from_db;
    }

    /**
     * @return bool
     */
    public function isDeletedFromStorage(): bool
    {
        return $this->deleted_from_storage;
    }

    /**
     * @return bool
     */
    public function isDeletedFromDb(): bool
    {
        return $this->deleted_from_db;
    }


}
