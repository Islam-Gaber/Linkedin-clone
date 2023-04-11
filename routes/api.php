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

    // Users routes
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/search', [UserController::class, 'search']);
    Route::get('/users/trashed', [UserController::class, 'getSoftDeletedUsers']);
    Route::get('users/filter', [UserController::class, 'getUsersByActiveStatus']);
    Route::get('/users/{user_uuid}', [UserController::class, 'show']);
    Route::put('/users/{user_uuid}', [UserController::class, 'update']);
    Route::delete('/users/{user_uuid}', [UserController::class, 'destroy']);

    // Profile routes
    Route::get('/profiles', [ProfileController::class, 'index']);
    Route::post('/profiles', [ProfileController::class, 'store']);
    Route::get('/profiles/{profile_uuid}', [ProfileController::class, 'show']);
    Route::put('/profiles/{profile_uuid}', [ProfileController::class, 'update']);

    // Experience routes
    Route::post('/profiles/{profile_uuid}/experiences', [ExperienceController::class, 'store']);
    Route::put('/profiles/{profile_uuid}/experiences/{experience_id}', [ExperienceController::class, 'update']);
    Route::delete('/profiles/{profile_uuid}/experiences/{experience_id}', [ExperienceController::class, 'destroy']);

    // Education routes
    Route::post('/profiles/{profile_uuid}/educations', [EducationController::class, 'store']);
    Route::put('/profiles/{profile_uuid}/educations/{education_id}', [EducationController::class, 'update']);
    Route::delete('/profiles/{profile_uuid}/educations/{education_id}', [EducationController::class, 'destroy']);

    // Skill routes
    Route::post('/profiles/{profile_uuid}/skills', [SkillController::class, 'store']);
    Route::put('/profiles/{profile_uuid}/skills/{skill_id}', [SkillController::class, 'update']);
    Route::delete('/profiles/{profile_uuid}/skills/{skill_id}', [SkillController::class, 'destroy']);

    // Connection routes
    Route::get('/connections', [ConnectionController::class, 'index']);
    Route::get('/connections/friends', [ConnectionController::class, 'myFriends']);
    Route::post('/connections/{user_uuid}', [ConnectionController::class, 'sendRequest']);
    Route::post('/connections/{connection_uuid}/accept', [ConnectionController::class, 'acceptRequest']);
    Route::post('/connections/{connection_uuid}/reject', [ConnectionController::class, 'rejectRequest']);
    Route::post('/connections/{connection_uuid}/block', [ConnectionController::class, 'blockConnection']);
    Route::delete('/connections/{connection_uuid}', [ConnectionController::class, 'destroy']);

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
