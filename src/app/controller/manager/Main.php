<?php

declare(strict_types=1);

namespace app\controller\manager;

use nova\framework\http\Response;
use nova\plugin\login\controller\BaseViewController;
use nova\plugin\tpl\Pjax;

class Main extends BaseViewController
{
    public function index(): Response
    {
        return Pjax::redirectTo($this->firstUri());
    }

    public function images(): Response
    {
        return $this->viewResponse->asTpl();
    }

    public function storage(): Response
    {
        return $this->viewResponse->asTpl();
    }

    protected function getMenu(): array
    {
        return [
            [
                'title' => '图片管理',
                'icon' => 'photo_library',
                'url' => '/manager/main/images',
                'pjax' => true,
            ],
            [
                'title' => '存储配置',
                'icon' => 'settings',
                'url' => '/manager/main/storage',
                'pjax' => true,
            ],
        ];
    }
}
