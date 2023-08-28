<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\Forum\ChapterController;
use App\Http\Controllers\Forum\TopicController;
use App\Http\Controllers\Forum\PostController;
use App\Http\Controllers\Forum\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServerManagementController;
use App\Http\Controllers\ControlPanelController;
use App\Http\Controllers\EmailVerificationController;

// use Illuminate\Foundation\Auth\EmailVerificationRequest;
// use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::controller(MainController::class)->group(function()
{
    Route::get('/', 'main');
    Route::get('/users', 'users')->middleware(['auth', 'verified']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::controller(ForumController::class)->group(function()
    {
        Route::get('/forum', 'forum');
        Route::post('/editor/image_upload', 'editor_image_upload')->name('upload');
    });
    Route::controller(ProfileController::class)->group(function()
    {
        Route::get('/profile/{id}/edit', 'profile_edit');
        Route::post('/profile/edit/public', 'profile_edit_public');
        Route::post('/profile/edit/private', 'profile_edit_private');
        Route::post('/profile/edit/password', 'profile_edit_password');
        Route::get('/profile/{id}', 'profile');
    });
});

//Route::get('/control-panel/{option}', [MainController::class, 'controlpanel'])->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::controller(EmailVerificationController::class)->group(function()
    {
        Route::get('/email/verify', 'view')->name('verification.notice');
        Route::post('/email/verification-notification', 'send_link')->middleware('throttle:6,1')->name('verification.resend');
        Route::get('/email/verify/{id}/{hash}', 'verify')->middleware('signed')->name('verification.verify');
    });
    Route::controller(ServerManagementController::class)->group(function()
    {
        Route::get('/server-management', 'servermanagement');
        Route::post('/server-management/add', 'servermanagement_add');
        Route::post('/server-management/edit', 'servermanagement_edit');
        Route::post('/server-management/delete', 'servermanagement_delete');
        Route::get('/server-management/console/{id}', 'servermanagement_console');
        Route::get('/server-management/console-update', 'servermanagement_console_update');
        Route::post('/server-management/console-runcommand', 'servermanagement_console_runcommand');
        Route::get('/server-management/players/{id}', 'servermanagement_players');
        Route::get('/server-management/lua/{id}', 'servermanagement_lua');
        Route::get('/server-management/errors/{id}', 'servermanagement_errors');
        Route::post('/server-management/error-delete', 'servermanagement_error_delete');
        Route::post('/server-management/task-add', 'servermanagement_task_add');
        Route::post('/server-management/task-delete', 'servermanagement_task_delete');
        Route::get('/server-management/tasks/{id}', 'servermanagement_tasks');
        Route::post('/server-management/task-change', 'servermanagement_task_change');
    });
    Route::controller(ControlPanelController::class)->group(function()
    {
        Route::get('/control-panel', 'statistics');
        Route::get('/control-panel/statistics', 'statistics');
        Route::get('/control-panel/servers', 'servers');
        Route::post('/control-panel/servers/add', 'servers_add');
        Route::post('/control-panel/servers/edit', 'servers_edit');
        Route::post('/control-panel/servers/delete', 'servers_delete');
        Route::get('/control-panel/permissions', 'permissions');
        Route::post('/control-panel/permissions-role/edit', 'permissions_role_edit');
        Route::get('/control-panel/forum', 'forum');
        Route::get('/control-panel/roles', 'roles');
        Route::post('/control-panel/roles-user/edit', 'roles_user_edit');
    });
    Route::controller(ChapterController::class)->group(function()
    {
        Route::post('/control-panel/forum/addchapter', 'add');
        Route::post('/control-panel/forum/editchapter', 'edit');
        Route::post('/control-panel/forum/deletechapter', 'delete');
    });
    Route::controller(TopicController::class)->group(function()
    {
        Route::get('/forum/{id}', 'view');
        Route::post('/control-panel/forum/addtopic', 'add');
        Route::post('/control-panel/forum/edittopic', 'edit');
        Route::post('/control-panel/forum/deletetopic', 'delete');
    });
    Route::controller(PostController::class)->group(function()
    {
        Route::get('/forum/post/{id}', 'view');
        Route::post('/forum/addpost', 'add');
        Route::post('/forum/editpost', 'edit');
        Route::post('/forum/deletepost', 'delete');
    });
    Route::controller(CommentController::class)->group(function()
    {
        Route::post('/forum/post/addcomment', 'add');
        Route::post('/forum/post/deletecomment', 'delete');
    });
});

// Route::get('/user/{id}/{name}', function ($id, $name) {
//     return "ID ". $id . " NAME " . $name;
// });

Auth::routes();
