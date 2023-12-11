<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

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


Route::post('/register', [AuthController::class, 'register']);
 Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::delete('/delete-news/{id}', [AdminController::class, 'deleteNews']);
    });

    Route::get('/home-page-news', [NewsController::class, 'getHomePageNews']);
    Route::post('/import-news', [NewsController::class, 'importNews']);
    Route::post('/news-store',[NewsController::class,'newsStore']);
    Route::post('/bookmark-news/{id}', [BookmarkController::class, 'bookmarkNews']);
    Route::delete('/unbookmark-news/{id}', [BookmarkController::class, 'unbookmarkNews']);
});
