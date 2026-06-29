<?php

declare(strict_types=1);

namespace app\utils;

use app\Application;

use function nova\framework\config;

use nova\framework\core\Context;
use nova\framework\core\Logger;
use nova\framework\event\EventManager;
use nova\framework\exception\AppExitException;
use nova\framework\http\Response;
use nova\plugin\login\db\Dao\UserDao;
use nova\plugin\tpl\ViewResponse;
use PDO;
use PDOException;
use Throwable;

class Installer
{
    public static function register(): void
    {
        EventManager::addListener('route.before', static function ($event, &$uri) {
            self::handle($uri);
        }, 0);
    }

    private static function handle(string $uri): void
    {
        if (self::isStaticAsset($uri)) {
            return;
        }

        $installed = (bool)config('installed');
        $isInstallRoute = str_starts_with($uri, '/install');

        if ($installed && !$isInstallRoute) {
            return;
        }

        if ($installed && $isInstallRoute) {
            throw new AppExitException(Response::asRedirect('/'), 'Already installed');
        }

        if (!$isInstallRoute) {
            throw new AppExitException(Response::asRedirect('/install'), 'Not installed');
        }

        $response = match ($uri) {
            '/install'        => self::showPage(),
            '/install/submit' => self::handleSubmit(),
            default           => Response::asRedirect('/install'),
        };

        throw new AppExitException($response, 'Exit by Installer');
    }

    private static function isStaticAsset(string $uri): bool
    {
        return str_starts_with($uri, '/static')
            || str_starts_with($uri, '/favicon');
    }

    private static function showPage(): Response
    {
        $view = new ViewResponse();
        $view->init('', [
            'title' => Application::SYSTEM_NAME,
        ]);
        return $view->asTpl('install');
    }

    private static function handleSubmit(): Response
    {
        $context = Context::instance();
        $req = $context->request();

        if (!$req->isPost()) {
            return self::json(405, '请用 POST 提交');
        }

        $db = [
            'host'     => trim((string)$req->post('db_host', '127.0.0.1')),
            'port'     => (int)$req->post('db_port', 3306),
            'username' => trim((string)$req->post('db_username', '')),
            'password' => (string)$req->post('db_password', ''),
            'db'       => trim((string)$req->post('db_name', '')),
            'charset'  => 'utf8mb4',
            'type'     => 'mysql',
        ];

        $webdav = [
            'url'      => trim((string)$req->post('webdav_url', '')),
            'username' => trim((string)$req->post('webdav_username', '')),
            'password' => (string)$req->post('webdav_password', ''),
        ];

        $systemName = trim((string)$req->post('system_name', '')) ?: Application::SYSTEM_NAME;

        if ($db['host'] === '' || $db['username'] === '' || $db['db'] === '') {
            return self::json(400, '数据库主机 / 账号 / 库名不能为空');
        }
        if ($webdav['url'] === '' || $webdav['username'] === '') {
            return self::json(400, 'WebDAV 地址和账号不能为空');
        }

        $dbError = self::testDatabase($db);
        if ($dbError !== null) {
            return self::json(400, '数据库连接失败：' . $dbError);
        }

        try {
            config('db.host', $db['host']);
            config('db.port', $db['port']);
            config('db.username', $db['username']);
            config('db.password', $db['password']);
            config('db.db', $db['db']);
            config('db.charset', $db['charset']);
            config('db.type', $db['type']);

            config('webdav.url', $webdav['url']);
            config('webdav.username', $webdav['username']);
            config('webdav.password', $webdav['password']);

            config('login.systemName', $systemName);

            UserDao::getInstance()->initTable();

            $adminPassword = self::readAdminPassword();

            config('installed', true);
        } catch (Throwable $e) {
            Logger::error('安装失败：' . $e->getMessage(), $e->getTrace());
            return self::json(500, '安装失败：' . $e->getMessage());
        }

        return self::json(200, '安装完成', [
            'data' => [
                'username' => 'admin',
                'password' => $adminPassword,
                'redirect' => '/login',
            ],
        ]);
    }

    private static function testDatabase(array $db): ?string
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $db['host'],
            $db['port'],
            $db['db'],
            $db['charset']
        );
        try {
            new PDO($dsn, $db['username'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);
            return null;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    private static function readAdminPassword(): ?string
    {
        $file = ROOT_PATH . DS . 'runtime' . DS . 'admin_password.txt';
        if (!is_file($file)) {
            return null;
        }
        $content = trim((string)file_get_contents($file));
        if ($content === '') {
            return null;
        }
        if (preg_match('/密码[:：]\s*(\w+)/u', $content, $m)) {
            return $m[1];
        }
        return null;
    }

    private static function json(int $code, string $msg, array $extra = []): Response
    {
        return Response::asJson(array_merge(['code' => $code, 'msg' => $msg], $extra));
    }
}
