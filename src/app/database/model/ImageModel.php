<?php

declare(strict_types=1);

namespace app\database\model;

use nova\plugin\orm\object\Model;

class ImageModel extends Model
{
    public string $name = '';
    public int $size = 0;

    public string $hash = '';
    public string $storage_path = '';
    public string $storage_type = 'local';

    public int $create_time = 0;
    public int $user_id = 0;

    public function getUnique(): array
    {
        return ['hash'];
    }
}
