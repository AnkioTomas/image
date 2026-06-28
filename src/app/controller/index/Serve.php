<?php

declare(strict_types=1);

namespace app\controller\index;

use app\database\dao\ImageDao;
use app\database\model\ImageModel;
use app\storage\StorageFactory;
use nova\framework\http\Response;
use nova\framework\route\Controller;

use function nova\framework\config;

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

    public function upload(): Response
    {
        $token = config('token') ?: '';
        if ($token === '') {
            return Response::asJson(['code' => 403, 'msg' => 'API 上传未启用']);
        }

        $auth  = $this->request->get('auth','');
        if(empty($auth)){
            $auth = $this->request->getHeaderValue('Authorization', '');
        }

        if (!hash_equals($auth, $token)) {
            return Response::asJson(['code' => 401, 'msg' => 'Token 无效']);
        }

        $file = $this->request->file('file');
        if ($file === null || $file->error !== UPLOAD_ERR_OK) {
            return Response::asJson(['code' => 400, 'msg' => '未收到文件']);
        }

        $ext = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff'];
        if (!in_array($ext, $allowed, true)) {
            return Response::asJson(['code' => 400, 'msg' => '不支持的文件类型']);
        }

        $fileHash = md5_file($file->tmp_name);

        $existing = ImageDao::getInstance()->findByHash($fileHash);
        if ($existing) {
            return Response::asJson([
                'code' => 200,
                'msg' => '文件已存在',
                'data' => ['url' => $this->request->getBasicAddress() . '/i/' . $fileHash],
            ]);
        }

        $storage = StorageFactory::create();
        $storagePath = $storage->store($file->tmp_name, $file->name);

        $model = new ImageModel();
        $model->name = $file->name;
        $model->user_id = 0;
        $model->size = $file->size;
        $model->create_time = time();
        $model->hash = $fileHash;
        $model->storage_path = $storagePath;
        $model->storage_type = config('storage.type') ?: 'local';

        ImageDao::getInstance()->insertModel($model, true);

        return Response::asJson([
            'code' => 200,
            'msg' => '上传成功',
            'data' => ['url' => $this->request->getBasicAddress() . '/i/' . $fileHash],
        ]);
    }
}
