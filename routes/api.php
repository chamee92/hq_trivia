<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ChallengeController;
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

    
    Route::post('challenge/add', [ChallengeController::class, 'addChallenge']);
    Route::post('challenge/image/add', [ChallengeController::class, 'addChallengeImage']);
    Route::post('challenge/video/add', [ChallengeController::class, 'addChallengeVideo']);
    Route::post('challenge/watch/add', [ChallengeController::class, 'addChallengeWatch']);
    Route::post('challenge/like/add', [ChallengeController::class, 'addChallengeLike']);
    Route::post('challenge/edit', [ChallengeController::class, 'editChallenge']);
    Route::post('challenge/next', [ChallengeController::class, 'nextChallenge']);
    Route::post('challenge/old/list', [ChallengeController::class, 'oldChallengeList']);
    Route::post('challenge/new/list', [ChallengeController::class, 'newChallengeList']);

    Route::post('question/add', [ChallengeController::class, 'addQuestion']);
    Route::post('question/delete', [ChallengeController::class, 'deleteQuestion']);
    Route::post('question/view', [ChallengeController::class, 'viewQuestion']);

    Route::post('challenge/user/list', [ChallengeController::class, 'viewChallengeUserList']);
    Route::post('user/challenge/list', [ChallengeController::class, 'viewUserChallengeList']);
    Route::post('challenge/user/question', [ChallengeController::class, 'viewChallengeUserQuestion']);
    Route::post('challenge/delete', [ChallengeController::class, 'deleteChallenge']);
    
    Route::post('question/complete', [ChallengeController::class, 'completeQuestion']);
    /*Route::post('challenge/complete', [ChallengeController::class, 'completeChallenge']);
    
    Route::post('payment/request', [ChallengeController::class, 'requestPayment']);
    Route::post('payment/confirm', [ChallengeController::class, 'confirmPayment']);
    Route::post('payment/pending', [ChallengeController::class, 'pendingPayment']);
    Route::post('payment/complete', [ChallengeController::class, 'completePayment']);
    Route::post('payment/reject', [ChallengeController::class, 'rejectPayment']);
    Route::post('ledger/view', [ChallengeController::class, 'viewLedger']);*/

});