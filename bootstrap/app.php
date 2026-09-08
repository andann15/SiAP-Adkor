<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// On Vercel, filesystem is read-only except /tmp
// Override storage path and bootstrap cache path to use /tmp
if (isset($_ENV['VERCEL']) || !is_writable(dirname(__DIR__) . '/storage/logs')) {
    $tmpStorage = '/tmp/storage';
    $dirs = [
        $tmpStorage,
        $tmpStorage . '/app',
        $tmpStorage . '/app/public',
        $tmpStorage . '/framework',
        $tmpStorage . '/framework/cache',
        $tmpStorage . '/framework/cache/data',
        $tmpStorage . '/framework/sessions',
        $tmpStorage . '/framework/testing',
        $tmpStorage . '/framework/views',
        $tmpStorage . '/logs',
        '/tmp/bootstrap/cache',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }
    $app->useStoragePath($tmpStorage);
    $app->useBootstrapPath('/tmp/bootstrap');
}

return $app;