<?php

declare(strict_types=1);

namespace app\controller\manager;

use app\database\dao\ImageDao;
use app\database\model\ImageModel;
use app\storage\StorageFactory;
use nova\framework\core\File;
use nova\framework\http\Response;
use nova\plugin\login\controller\BaseAPIController;
use nova\plugin\upload\UploadController;

class Image extends BaseAPIController
{
    public function list(): Response
    {
        $page = (int)$this->request->get('page', 1);
        $size = (int)$this->request->get('pageSize', 20);

        $result = ImageDao::getInstance()->listByUser($this->userModel->id, $page, $size);

        return Response::asJson([
            'code' => 200,
            'count' => $result['total'],
            'data' => $result['data'],
        ]);
    }


    public function upload():Response
    {
        return UploadController::getInstance()->upload($this->request);
    }

    public function save(): Response
    {
        $filename = $this->request->post('file','');

        if(empty($filename)) return Response::asJson(['code' => 404,'msg' => 'file not found']);

        $file = UploadController::getInstance()->dir($filename);

        if(!File::exists($file)){
            return Response::asJson(['code' => 404,'msg' => 'file not found']);
        }
        $fileSize = filesize($file) ;
        $fileHash = md5_file($file);


        $model =   ImageDao::getInstance()->findByHash($fileHash);
        if($model){
            return Response::asJson(['code' => 200,'msg' => 'file exists', 'data' => $model]);
        }

        //TODO 上传前优化，添加水印？

        // 上传到存储空间来
        $storage = StorageFactory::create();
        $storagePath = $storage->store($file, $filename);
        $model = new ImageModel();

        $model->name = $filename;
        $model->user_id = $this->userModel->id;
        $model->size = $fileSize;
        $model->create_time = time();
        $model->hash = $fileHash;
        $model->storage_path = $storagePath;
        $model->storage_type = 'webdav';

        ImageDao::getInstance()->insertModel($model, true);

        return Response::asJson([
            'code' => 200,
            'msg' => '上传成功',
            'data' => $this->request->getBasicAddress()."/i/".$fileHash,
        ]);
    }
    public function delete(): Response
    {
        $id = (int)$this->request->post('id', 0);
        $model = ImageDao::getInstance()->find(null, ['id' => $id]);

        if (!$model instanceof ImageModel) {
            return Response::asJson(['code' => 404, 'msg' => '图片不存在']);
        }

        $storage = StorageFactory::create($model->storage_type);
        $storage->delete($model->storage_path);

        ImageDao::getInstance()->remove($id);

        return Response::asJson(['code' => 200, 'msg' => '删除成功']);
    }
}
