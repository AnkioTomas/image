<?php

declare(strict_types=1);

namespace app\storage;

use nova\framework\http\Response;

interface StorageInterface
{
    /**
     * 将本地文件存入存储后端
     * @return string 存储路径
     */
    public function store(string $localPath, string $targetName): string;

    public function delete(string $storagePath): bool;

    public function serve(string $storagePath, ?string $range = null): Response;
}
