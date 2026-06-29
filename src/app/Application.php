<?php

declare(strict_types=1);

namespace app;

use app\utils\Installer;
use nova\framework\App;
use nova\framework\event\EventManager;
use nova\framework\route\Route;
use nova\plugin\login\LoginTpl;

use function nova\framework\route;

class Application extends App
{
    public function onFrameworkStart(): void
    {
        Installer::register();

        $adminRoute = ['manager', 'main'];
        EventManager::trigger('admin.router', $adminRoute);

        Route::getInstance()
            ->get('/', route('manager', 'main', 'index'))
            ->get('/i/{hash}', route('index', 'serve', 'image'))
            ->post('/api/upload', route('index', 'serve', 'upload'))
        ;

    }

    public const string SYSTEM_NAME = "Ankioの图床";
}
