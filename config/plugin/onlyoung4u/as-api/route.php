<?php

use app\admin\controller\GptArticleController;
use app\admin\controller\JobController;
use app\admin\controller\TimingController;
use Onlyoung4u\AsApi\BaseRoute;
use Webman\Route;
use Onlyoung4u\AsApi\Middleware\ActionLog;
use Onlyoung4u\AsApi\Middleware\Auth;
use Onlyoung4u\AsApi\Middleware\Permission;

// 加载基础路由
BaseRoute::load();

// 公共路由
Route::group('/admin', function () {
    // 获取可发布的任务
    Route::get('/word', [JobController::class, 'word'])->name('automation.word');
})->middleware([
    config('plugin.onlyoung4u.as-api.app.middleware.auth', Auth::class),
    config('plugin.onlyoung4u.as-api.app.middleware.action_log', ActionLog::class),
]);

// 权限路由
Route::group('/admin', function () {
    /****** 任务相关 ******/

    // 创建任务
    Route::get('/job', [JobController::class, 'list'])->name('automation.thesaurus');
    Route::post('/job', [JobController::class, 'create'])->name('automation.thesaurus.create');
    Route::put('/job/start/{id:\d+}', [JobController::class, 'start'])->name('automation.thesaurus.start');
    Route::put('/job/stop/{id:\d+}', [JobController::class, 'stop'])->name('automation.thesaurus.stop');
    Route::delete('/job/{id:\d+}', [JobController::class, 'delete'])->name('automation.thesaurus.delete');

    // 定时任务管理
    Route::get('/timing', [TimingController::class, 'list'])->name('automation.scheduled');
    Route::post('/timing', [TimingController::class, 'create'])->name('automation.scheduled.create');
    Route::put('/timing/{id:\d+}', [TimingController::class, 'update'])->name('automation.scheduled.update');
    Route::put('/timing/start/{id:\d+}', [TimingController::class, 'start'])->name('automation.scheduled.start');
    Route::put('/timing/stop/{id:\d+}', [TimingController::class, 'stop'])->name('automation.scheduled.stop');
    Route::delete('/timing/{id:\d+}', [TimingController::class, 'delete'])->name('automation.scheduled.delete');

    // 文章管理
    Route::get('/article', [GptArticleController::class, 'list'])->name('content.list');
    Route::delete('/article/{id:\d+}', [GptArticleController::class, 'del'])->name('content.list.delete');
   
})->middleware([
    config('plugin.onlyoung4u.as-api.app.middleware.auth', Auth::class),
    config('plugin.onlyoung4u.as-api.app.middleware.action_log', ActionLog::class),
    config('plugin.onlyoung4u.as-api.app.middleware.permission', Permission::class),
]);