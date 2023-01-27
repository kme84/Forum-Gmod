<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServerManagementController;
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

Route::controller(MainController::class)->group(function()
{
    Route::get('/', 'main');
    Route::get('/users', 'users')->middleware(['auth']);
});

Route::controller(ForumController::class)->group(function()
{
    Route::get('/forum', 'forum');
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

Route::controller(ProfileController::class)->group(function()
{
    Route::get('/profile/{id}/edit', 'profile_edit');
    Route::post('/profile/edit/public', 'profile_edit_public');
    Route::post('/profile/edit/private', 'profile_edit_private');
    Route::post('/profile/edit/password', 'profile_edit_password');
    Route::get('/profile/{id}', 'profile');
});

Route::controller(ServerManagementController::class)->group(function()
{
    Route::get('/server-management', 'servermanagement');
    Route::post('/server-management/add', 'servermanagement_add');
    Route::post('/server-management/delete', 'servermanagement_delete');
    Route::get('/server-management/console/{id}', 'servermanagement_console');
    Route::get('/server-management/console-update', 'servermanagement_console_update');
    Route::post('/server-management/console-runcommand', 'servermanagement_console_runcommand');
    Route::get('/server-management/players/{id}', 'servermanagement_players');
    Route::get('/server-management/lua/{id}', 'servermanagement_lua');
    Route::get('/server-management/errors/{id}', 'servermanagement_errors');
});

//Route::get('/control-panel/{option}', [MainController::class, 'controlpanel'])->middleware('auth');

Route::controller(ControlPanelController::class)->group(function()
{
    Route::get('/control-panel', 'statistics');
    Route::get('/control-panel/statistics', 'statistics');
    Route::get('/control-panel/servers', 'servers');
    Route::post('/control-panel/servers/add', 'servers_add');
    Route::post('/control-panel/servers/delete', 'servers_delete');
});

// Route::get('/user/{id}/{name}', function ($id, $name) {
//     return "ID ". $id . " NAME " . $name;
// });


Auth::routes();