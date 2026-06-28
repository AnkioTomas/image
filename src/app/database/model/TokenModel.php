<?php

namespace app\database\model;

use nova\plugin\orm\object\Model;

class TokenModel extends Model
{
    public string $token = "";
    public int $user_id = 0;

    public function getUnique(): array
    {
        return [
            ['token','user_id'],
        ];
    }
}