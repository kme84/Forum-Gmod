<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ControlPanelController;

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

Route::get('/', [MainController::class, 'main']);
Route::get('/forum', [MainController::class, 'forum']);
Route::get('/users', [MainController::class, 'users']);
//Route::get('/control-panel/{option}', [MainController::class, 'controlpanel'])->middleware('auth');
Route::get('/server-management', [MainController::class, 'servermanagement'])->middleware('auth');
Route::post('/server-management/add', [MainController::class, 'servermanagement_add'])->middleware('auth');
Route::post('/server-management/delete', [MainController::class, 'servermanagement_delete'])->middleware('auth');
Route::get('/server-management/console/{id}', [MainController::class, 'servermanagement_console'])->middleware('auth');
Route::get('/server-management/console-update', [MainController::class, 'servermanagement_console_update'])->middleware('auth');
Route::post('/server-management/console-runcommand', [MainController::class, 'servermanagement_console_runcommand'])->middleware('auth');
Route::get('/server-management/players/{id}', [MainController::class, 'servermanagement_players'])->middleware('auth');
Route::get('/server-management/lua/{id}', [MainController::class, 'servermanagement_lua'])->middleware('auth');
Route::get('/server-management/errors/{id}', [MainController::class, 'servermanagement_errors'])->middleware('auth');
Route::get('/profile/edit', [MainController::class, 'profile_edit'])->middleware('auth');
Route::post('/profile/edit/public', [MainController::class, 'profile_edit_public'])->middleware('auth');
Route::post('/profile/edit/private', [MainController::class, 'profile_edit_private'])->middleware('auth');
Route::post('/profile/edit/password', [MainController::class, 'profile_edit_password'])->middleware('auth');
Route::get('/profile/{id}', [MainController::class, 'profile'])->middleware(['auth', 'rights:1']);

Route::controller(ControlPanelController::class)->group(function()
{
    Route::get('/control-panel', 'statistics');
    Route::get('/control-panel/statistics', 'statistics');
    Route::get('/control-panel/servers', 'servers');
    Route::post('/control-panel/servers/add', 'servers_add');
    Route::post('/control-panel/servers/delete', 'servers_delete');

    Route::post('/forum/addchapter', 'forum_addchapter');
    Route::post('/forum/deletechapter', 'forum_deletechapter');
    Route::post('/forum/addtopic', 'forum_addtopic');
    Route::post('/forum/deletetopic', 'forum_deletetopic');
    Route::post('/forum/addpost', 'forum_addpost');
    Route::post('/forum/deletepost', 'forum_deletepost');
    Route::post('/forum/post/addcomment', 'forum_addcomment');
    Route::post('/forum/post/deletecomment', 'forum_deletecomment');
    Route::get('/forum/{id}', 'forum_topic');
    Route::get('/forum/post/{id}', 'forum_post');
    Route::post('/editor/image_upload', 'editor_image_upload')->name('upload');
});

Route::get('/user/{id}/{name}', function ($id, $name) {
    return "ID ". $id . " NAME " . $name;
});

Auth::routes();