<?php

declare(strict_types=1);

namespace app\storage;

class StorageFactory
{
    public static function create(?string $type = null): StorageInterface
    {
        return new WebdavStorage();
    }
}
