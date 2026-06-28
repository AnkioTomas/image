<?php

declare(strict_types=1);

namespace app\storage;

use nova\framework\http\Response;
use nova\plugin\webdav\SimpleWebDAVClient;

use function nova\framework\config;

class WebdavStorage implements StorageInterface
{
    private SimpleWebDAVClient $client;
    private string $basePath = '/images';

    public function __construct()
    {
        $cfg = config('webdav');
        $this->client = new SimpleWebDAVClient(
            $cfg['url'] ?? '',
            $cfg['username'] ?? null,
            $cfg['password'] ?? null,
        );
    }

    public function store(string $localPath, string $targetName): string
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $remotePath = $this->basePath . '/' . $year . '/' . $month . '/' . $day;
        $this->ensureDir($remotePath);

        $remoteFile = $remotePath . '/' . $targetName;
        $this->client->upload($localPath, $remoteFile);
        unlink($localPath);

        return $remoteFile;
    }

    public function delete(string $storagePath): bool
    {
        return $this->client->delete($storagePath);
    }

    public function serve(string $storagePath, ?string $range = null): Response
    {
        return $this->client->downloadToResponse($storagePath, null, $range);
    }

    private function ensureDir(string $path): void
    {
        $parts = explode('/', trim($path, '/'));
        $current = '';
        foreach ($parts as $part) {
            $current .= '/' . $part;
            if (!$this->client->isDirectory($current)) {
                $this->client->mkdir($current);
            }
        }
    }
}
