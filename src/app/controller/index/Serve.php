<?php

declare(strict_types=1);

namespace app\controller\index;

use app\database\dao\ImageDao;
use app\database\model\ImageModel;
use app\storage\StorageFactory;
use nova\framework\http\Response;
use nova\framework\route\Controller;

class Serve extends Controller
{
    public function image(string $hash): Response
    {
        $model = ImageDao::getInstance()->findByHash($hash);
        if (!$model instanceof ImageModel) {
            return Response::asText('404 Not Found');
        }

        $range = $_SERVER['HTTP_RANGE'] ?? null;
        $storage = StorageFactory::create($model->storage_type);

        return $storage->serve($model->storage_path, $range);
    }
}
