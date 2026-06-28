<?php

declare(strict_types=1);

namespace app\controller\index;

use app\database\dao\ImageDao;
use app\database\dao\TokenDao;
use app\database\model\ImageModel;
use app\storage\StorageFactory;
use nova\framework\core\Logger;
use nova\framework\http\Response;
use nova\framework\http\UploadModel;
use nova\framework\route\Controller;

use function nova\framework\config;
use function nova\framework\dump;

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

        $auth  = $this->request->get('auth','');
        if(empty($auth)){
            $auth = $this->request->getHeaderValue('Authorization', '');
        }

        $tokenModel = TokenDao::getInstance()->getByToken($auth);


        if (empty($tokenModel) || !hash_equals($auth, $tokenModel->token)) {
            return Response::asJson(['code' => 401, 'msg' => 'Token 无效']);
        }

        $file = $this->request->file('file');

        if (empty($file)) {
            $tmp = tempnam(sys_get_temp_dir(), '');
            $json = $this->request->json();
            if(is_array($json)){
                file_put_contents($tmp, base64_decode($json['file']));
                $file = new UploadModel();
                $file->tmp_name = $tmp;
                $file->name = "temp.png";
                $file->error = UPLOAD_ERR_OK;
                $file->size = filesize($tmp);
            }else{
                return Response::asJson(['code' => 400, 'msg' => '未收到文件']);
            }


        }

        if ($file === null || $file->error !== UPLOAD_ERR_OK) {
            return Response::asJson(['code' => 400, 'msg' => '未收到文件']);
        }


        $fileHash = md5_file($file->tmp_name);

        $existing = ImageDao::getInstance()->findByHash($fileHash);
        if ($existing) {
            return Response::asJson([
                'code' => 200,
                'msg' => '文件已存在',
                'data' =>  $this->request->getBasicAddress() . '/i/' . $fileHash,
            ]);
        }

        $storage = StorageFactory::create();
        $storagePath = $storage->store($file->tmp_name, "$fileHash.png");

        $model = new ImageModel();
        $model->name = "$fileHash.png";
        $model->user_id = $tokenModel->user_id;
        $model->size = $file->size;
        $model->create_time = time();
        $model->hash = $fileHash;
        $model->storage_path = $storagePath;
        $model->storage_type = 'webdav';

        ImageDao::getInstance()->insertModel($model);

        return Response::asJson([
            'code' => 200,
            'msg' => '上传成功',
            'data' =>  $this->request->getBasicAddress() . '/i/' . $fileHash,
        ]);
    }
}
