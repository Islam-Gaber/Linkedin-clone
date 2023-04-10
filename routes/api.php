<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SkillController;
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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/search', [UserController::class, 'search']);
    Route::get('/users/trashed', [UserController::class, 'getSoftDeletedUsers']);
    Route::get('users/filter', [UserController::class, 'getUsersByActiveStatus']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Profile routes
    Route::get('/profiles', [ProfileController::class, 'index']);
    Route::post('/profiles', [ProfileController::class, 'store']);
    Route::get('/profiles/{uuid}', [ProfileController::class, 'show']);
    Route::put('/profiles/{uuid}', [ProfileController::class, 'update']);

    // Experience routes
    Route::post('/profiles/{uuid}/experiences', [ExperienceController::class, 'store']);
    Route::put('/profiles/{uuid}/experiences/{id}', [ExperienceController::class, 'update']);
    Route::delete('/profiles/{uuid}/experiences/{id}', [ExperienceController::class, 'destroy']);

    // Education routes
    Route::post('/profiles/{uuid}/educations', [EducationController::class, 'store']);
    Route::put('/profiles/{uuid}/educations/{id}', [EducationController::class, 'update']);
    Route::delete('/profiles/{uuid}/educations/{id}', [EducationController::class, 'destroy']);

    // Skill routes
    Route::post('/profiles/{uuid}/skills', [SkillController::class, 'store']);
    Route::put('/profiles/{uuid}/skills/{id}', [SkillController::class, 'update']);
    Route::delete('/profiles/{uuid}/skills/{id}', [SkillController::class, 'destroy']);

    // Connection routes
    Route::get('/connections', [ConnectionController::class, 'index']);
    Route::get('/connections/friends', [ConnectionController::class, 'myFriends']);
    Route::post('/connections/{id}', [ConnectionController::class, 'sendRequest']);
    Route::post('/connections/{id}/accept', [ConnectionController::class, 'acceptRequest']);
    Route::post('/connections/{id}/reject', [ConnectionController::class, 'rejectRequest']);
    Route::post('/connections/{id}/block', [ConnectionController::class, 'blockConnection']);
    Route::delete('/connections/{id}', [ConnectionController::class, 'destroy']);

    // Post routes
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // Post Like routes
    Route::post('/posts/{id}/like', [PostLikeController::class, 'like']);
    Route::delete('/posts/{id}/like', [PostLikeController::class, 'unlike']);

    // Post Comment routes
    Route::post('/posts/{id}/comments', [PostCommentController::class, 'store']);
    Route::put('/posts/{post_id}/comments/{comment_id}', [PostCommentController::class, 'update']);
    Route::delete('/posts/{post_id}/comments/{comment_id}', [PostCommentController::class, 'destroy']);
});
