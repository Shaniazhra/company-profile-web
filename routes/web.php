<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PipelineController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home', ['locale' => 'id']));

Route::prefix('{locale}')
    ->where(['locale' => 'id|en'])
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::view('/tentang-kami', 'about')->name('about');
        Route::get('/alur-produksi', [PipelineController::class, 'index'])->name('pipeline');
    });