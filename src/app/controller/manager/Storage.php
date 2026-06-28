<?php

declare(strict_types=1);

namespace app\controller\manager;

use nova\framework\http\Response;
use nova\plugin\login\controller\BaseAPIController;

use function nova\framework\config;

class Storage extends BaseAPIController
{
    public function config(): Response
    {
        if ($this->request->isGet()) {
            return Response::asJson([
                'code' => 200,
                'data' => [
                    'type' => config('storage.type') ?: 'local',
                ],
            ]);
        }

        $type = $this->request->post('type', 'local');
        if (!in_array($type, ['local', 'webdav'], true)) {
            return Response::asJson(['code' => 400, 'msg' => '不支持的存储类型']);
        }

        config('storage.type', $type);

        return Response::asJson(['code' => 200, 'msg' => '保存成功']);
    }
}
