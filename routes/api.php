<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login')->name('login');
});

Route::controller(PostController::class)->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
            // Route::middleware('auth.api')->group(function () {
        Route::post('/create-post', 'CreatePost');
        Route::get('/postlist', 'PostLists');
        Route::get('/postlist/{id}', 'GetSinglePost');
        Route::get('/edit/{id}', 'EditPost');
        Route::put('/update-post/{id}', 'UpdatePost');
        Route::delete('/delete-post/{id}', 'DeletePost');
        Route::post('/logout', 'Logout');
    });
});

