<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1/'], function () {

    // Authorization
    Route::post('/user/login', [App\Http\Controllers\Api\AuthenticationController::class, 'login']);
    Route::post('/user/signup', [App\Http\Controllers\Api\AuthenticationController::class, 'signup']);
    Route::get('/regions', [App\Http\Controllers\Api\RegionController::class, 'region']);
    Route::delete('/user/logout', [App\Http\Controllers\Api\AuthenticationController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/user/send-otp', [App\Http\Controllers\Api\AuthenticationController::class, 'sendOtp']);
    Route::post('/user/verify-otp', [App\Http\Controllers\Api\AuthenticationController::class, 'verifyOtp']);
    Route::patch('/user/reset-password', [App\Http\Controllers\Api\AuthenticationController::class, 'resetPassword']);

    // Profiles
    Route::get('/players', [App\Http\Controllers\Api\PlayerProfileController::class, 'userPlayer'])->middleware('auth:sanctum');
    Route::post('/user/update-profile', [App\Http\Controllers\Api\PlayerProfileController::class, 'updateProfile'])->middleware('auth:sanctum');
    Route::get('/user/profile', [App\Http\Controllers\Api\PlayerProfileController::class, 'profile'])->middleware('auth:sanctum');
    Route::patch('/user/update-experty', [App\Http\Controllers\Api\PlayerProfileController::class, 'updateExpertyLevel'])->middleware('auth:sanctum');

    // Categories
    Route::get('/categories-ladders', [App\Http\Controllers\Api\CategoryController::class, 'categoriesLadders'])->middleware('auth:sanctum');

    // Ranking
    Route::get('/users/rankings', [App\Http\Controllers\Api\RankController::class, 'ranking'])->middleware('auth:sanctum');
    Route::get('/users/other-player-ranking', [App\Http\Controllers\Api\RankController::class, 'otherPlayerRanking'])->middleware('auth:sanctum');

    // Matches
    Route::post('/match/create', [App\Http\Controllers\Api\MatchController::class, 'createMatch'])->middleware('auth:sanctum');
    Route::patch('/match/update', [App\Http\Controllers\Api\MatchController::class, 'updateMatch'])->middleware('auth:sanctum');

    Route::get('/user/proposals', [App\Http\Controllers\Api\MatchController::class, 'getProposal'])->middleware('auth:sanctum');
    Route::get('/user/challenges', [App\Http\Controllers\Api\MatchController::class, 'getChallenge'])->middleware('auth:sanctum');
    Route::get('/user/matches/history', [App\Http\Controllers\Api\MatchController::class, 'getMatchHistory'])->middleware('auth:sanctum');
    Route::get('/user/matches/scheduled', [App\Http\Controllers\Api\MatchController::class, 'getMatchScheduled'])->middleware('auth:sanctum');
    Route::get('/users/other-player-matches', [App\Http\Controllers\Api\MatchController::class, 'getOtherPlayerMatch'])->middleware('auth:sanctum');
    Route::get('/user/ranking-matches', [App\Http\Controllers\Api\MatchController::class, 'getRankingMatches'])->middleware('auth:sanctum');


    // Inbox
    Route::get('/user/inbox', [App\Http\Controllers\Api\ChatController::class, 'getInbox'])->middleware('auth:sanctum');

    Route::post(
        '/users/notification',
        [App\Http\Controllers\Api\ChatController::class, 'userNotification']
    )->middleware('auth:sanctum');


    // Miscellaneous
    Route::get('/users/relaunch-data', [App\Http\Controllers\Api\MiscellaneousController::class, 'getRelaunchData'])->middleware('auth:sanctum');
    Route::get('/users/teams', [App\Http\Controllers\Api\MiscellaneousController::class, 'getTeams'])->middleware('auth:sanctum');
    Route::get('/user/notifications', [App\Http\Controllers\Api\MiscellaneousController::class, 'getNotification'])->middleware('auth:sanctum');

    // Report Issue
    Route::post('/user/report-issue', [App\Http\Controllers\Api\IssueController::class, 'reportIssues'])->middleware('auth:sanctum');;

    // Reject Payment
    Route::patch('reject-payment', [App\Http\Controllers\Api\PaymentController::class, 'rejectPayment'])->middleware('auth:sanctum');
    Route::post('request-payment', [App\Http\Controllers\Api\PaymentController::class, 'requestPayment'])->middleware('auth:sanctum');

    //Create team
    Route::post('create-team', [App\Http\Controllers\Api\TeamController::class, 'createTeam'])->middleware('auth:sanctum');

    // Playoff status
    Route::get('/playoff-status', [App\Http\Controllers\Api\PlayerProfileController::class, 'playoffStatus'])->middleware('auth:sanctum');
});


Route::group(['prefix' => 'v1/dummy/'], function () {

    // Authorization
    Route::post('/user/login', [App\Http\Controllers\Api\AuthenticationController::class, 'dummylogin']);
    Route::post('/user/signup', [App\Http\Controllers\Api\AuthenticationController::class, 'dummysignup']);
    Route::get('/regions', [App\Http\Controllers\Api\RegionController::class, 'dummyregion']);
    Route::delete('/user/logout', [App\Http\Controllers\Api\AuthenticationController::class, 'dummylogout']);
    Route::post('/user/send-otp', [App\Http\Controllers\Api\AuthenticationController::class, 'dummysendOtp']);
    Route::post('/user/verify-otp', [App\Http\Controllers\Api\AuthenticationController::class, 'dummyverifyOtp']);
    Route::post('/user/reset-password', [App\Http\Controllers\Api\AuthenticationController::class, 'dummyresetPassword']);

    // Profiles
    Route::get('/players', [App\Http\Controllers\Api\PlayerProfileController::class, 'dummyuserPlayer']);
    Route::patch('/user/update-profile', [App\Http\Controllers\Api\PlayerProfileController::class, 'dummyupdateProfile']);
    Route::get('/user/profile', [App\Http\Controllers\Api\PlayerProfileController::class, 'dummyprofile']);
    Route::patch('/user/update-experty', [App\Http\Controllers\Api\PlayerProfileController::class, 'dummyupdateExpertyLevel']);

    // Categories
    Route::get('/categories-ladders', [App\Http\Controllers\Api\CategoryController::class, 'dummycategoriesLadders']);

    // Ranking
    Route::get('/users/rankings', [App\Http\Controllers\Api\RankController::class, 'dummyranking']);
    Route::get('/users/other-player-ranking', [App\Http\Controllers\Api\RankController::class, 'dummyotherPlayerRanking']);

    // Matches
    Route::post('/match/create', [App\Http\Controllers\Api\MatchController::class, 'dummycreateMatch']);
    Route::patch('/match/update', [App\Http\Controllers\Api\MatchController::class, 'dummyupdateMatch']);
    Route::get('/user/proposals', [App\Http\Controllers\Api\MatchController::class, 'dummygetProposal']);
    Route::get('/user/challenges', [App\Http\Controllers\Api\MatchController::class, 'dummygetChallenge']);
    Route::get('/user/matches/history', [App\Http\Controllers\Api\MatchController::class, 'dummygetMatchHistory']);
    Route::get('/user/matches/scheduled', [App\Http\Controllers\Api\MatchController::class, 'dummygetMatchScheduled']);
    Route::get('/users/other-player-matches', [App\Http\Controllers\Api\MatchController::class, 'dummygetOtherPlayerMatch']);
    Route::get('/user/ranking-matches', [App\Http\Controllers\Api\MatchController::class, 'dummygetRankingMatches']);


    // Inbox
    Route::get('/user/inbox', [App\Http\Controllers\Api\ChatController::class, 'dummygetInbox']);


    // Miscellaneous
    Route::get('/users/relaunch-data', [App\Http\Controllers\Api\MiscellaneousController::class, 'dummygetRelaunchData']);
    Route::get('/user/teams', [App\Http\Controllers\Api\MiscellaneousController::class, 'dummygetTeams']);
    Route::get('/user/notifications', [App\Http\Controllers\Api\MiscellaneousController::class, 'dummygetNotification']);

    // Report Issue
    Route::get('/user/report-issue', [App\Http\Controllers\Api\IssueController::class, 'dummygetIssues']);
});
