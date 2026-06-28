<?php

declare(strict_types=1);

namespace app\controller\manager;

use nova\framework\http\Response;
use nova\plugin\login\controller\BaseAPIController;

use function nova\framework\config;
use function nova\framework\uuid;

class Storage extends BaseAPIController
{
    public function config(): Response
    {
        if ($this->request->isGet()) {

            $token = config('token');
            if(empty($token)){
                $token = uuid();
                config('token', $token);
            }

            return Response::asJson([
                'code' => 200,
                'data' => [
                    'token' => $token,
                ],
            ]);
        }


        $token = $this->request->post('token', '');
        config('token', $token);

        return Response::asJson(['code' => 200, 'msg' => '保存成功']);
    }
}
