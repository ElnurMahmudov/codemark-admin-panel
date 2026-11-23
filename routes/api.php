<?php

use App\Http\Controllers\Api\V1\AnalyticsApiController;
use App\Http\Controllers\Api\V1\BlogApiController;
use App\Http\Controllers\Api\V1\CustomerLogoApiController;
use App\Http\Controllers\Api\V1\ProjectApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Analitika
    Route::post('/analytics/track', [AnalyticsApiController::class, 'track']);

    // Müştəri loqoları
    Route::get('/customer-logos', [CustomerLogoApiController::class, 'index']);

    // Bloq
    Route::get('/blog', [BlogApiController::class, 'index']);
    Route::get('/blog-categories', [BlogApiController::class, 'categories']);
    Route::get('/blog/{slug}', [BlogApiController::class, 'show']);

    // Layihələr / Portfel
    Route::get('/projects', [ProjectApiController::class, 'index']);
    Route::get('/project-categories', [ProjectApiController::class, 'categories']);
    Route::get('/projects/{code}', [ProjectApiController::class, 'show']);
});
