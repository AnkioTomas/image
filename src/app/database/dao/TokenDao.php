<?php

namespace app\database\dao;

use app\database\model\TokenModel;
use nova\plugin\orm\object\Dao;

class TokenDao extends Dao
{
    function getByUid(int $uid): ?TokenModel
    {
        return $this->find(null, ['user_id' => $uid]);
    }

    function getByToken(string $token): ?TokenModel
    {
        return $this->find(null, ['token' => $token]);
    }

    function setByUid(int $uid, string $token): TokenModel
    {
        $model = $this->getByUid($uid);

        if(empty($model)){
            $model = new TokenModel();
            $model->user_id = $uid;
            $model->token = $token;
            $model->id = $this->insertModel($model);
        }else{
            $model->token = $token;
            $this->updateModel($model);
        }
        return $model;
    }
}