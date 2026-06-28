<?php

declare(strict_types=1);

namespace app\controller\manager;

use app\database\dao\TokenDao;
use nova\framework\http\Response;
use nova\plugin\login\controller\BaseAPIController;

use function nova\framework\config;
use function nova\framework\uuid;

class Storage extends BaseAPIController
{
    public function config(): Response
    {
        if ($this->request->isGet()) {

            $tokenModel = TokenDao::getInstance()->getByUid($this->userModel->id);
            if(empty($tokenModel) || empty($tokenModel->token)){
                $token = uuid();
                $tokenModel = TokenDao::getInstance()->setByUid($this->userModel->id, $token);
            }

            return Response::asJson([
                'code' => 200,
                'data' => $tokenModel,
            ]);
        }


        $token = $this->request->post('token', '');
        TokenDao::getInstance()->setByUid($this->userModel->id, $token);

        return Response::asJson(['code' => 200, 'msg' => '保存成功']);
    }
}
