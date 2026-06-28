<?php

declare(strict_types=1);

namespace app\database\dao;

use app\database\model\ImageModel;
use nova\plugin\orm\object\Dao;

class ImageDao extends Dao
{
    public function findByUri(string $uriName): ?ImageModel
    {
        return $this->find(null, ['uri_name' => $uriName]);
    }

    public function findByHash(string $hash): ?ImageModel
    {
        return $this->find(null, ['hash' => $hash]);
    }

    public function remove(int $id): void
    {
        $this->delete()->where(['id' => $id])->commit();
    }

    public function listByUser(int $userId, int $page, int $size): array
    {
        $where = [];
        if ($userId > 0) {
            $where['user_id'] = $userId;
        }
        return $this->getAll([], $where, $page, $size, 'id',false);
    }
}
