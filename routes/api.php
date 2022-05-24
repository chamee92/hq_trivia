<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'autheticate']);

//Route::post('version/latest', [SettingController::class, 'latestVersion']);
Route::group(['middleware' => ['jwt.verify']], function () {  
    Route::post('logout', [UserController::class, 'logout']); 
    Route::post('password/reset', [UserController::class, 'passwordChange']);
    Route::post('user/data', [UserController::class, 'getAuthenticatedUser']);
    Route::post('user/list', [UserController::class, 'usersData']);
    Route::post('user/manage', [UserController::class, 'userManage']);

});