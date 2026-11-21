<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\AboutController;
use App\Http\Controllers\Front\ServiceController;
use App\Http\Controllers\Front\TourController;
use App\Http\Controllers\Front\FaqController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\ContactController;

//------------------------
//    Menu Routes 
//------------------------

    // --- Home --- //
    Route::get('/', [HomeController::class, 'index'])->name('front.home');

    // --- About --- //
    Route::get('/haqqimizda', [AboutController::class, 'index'])->name('front.about');

    // --- Services --- //
    Route::get('/xidmetler', [ServiceController::class, 'index'])->name('front.services');
    Route::get('/xidmetler/{slug}', [ServiceController::class, 'show'])->name('front.services.show');

    // --- Tours --- //
    Route::get('/turlar', [TourController::class, 'index'])->name('front.tours');
    Route::get('/turlar/{slug}', [TourController::class, 'show'])->name('front.tours.show');

    // --- FAQ --- //
    Route::get('/faq', [FaqController::class, 'index'])->name('front.faq');

    // --- Blog --- //
    Route::get('/blog', [BlogController::class, 'index'])->name('front.blog');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('front.blog.show');
    
    // --- Contact --- //
    Route::get('/elaqe', [ContactController::class, 'index'])->name('front.contact');
    Route::post('/elaqe', [ContactController::class, 'send'])->name('front.contact.send');
