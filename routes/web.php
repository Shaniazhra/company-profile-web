<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/* ---------- Situs publik (tamu, ada prefix bahasa) ---------- */
Route::get('/', fn () => redirect()->route('home', ['locale' => 'en']));

Route::prefix('{locale}')
    ->where(['locale' => 'en'])
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::view('/about', 'about')->name('about');

        // Aktifkan setelah PipelineController dibuat:
        // Route::get('/alur-produksi', [\App\Http\Controllers\PipelineController::class, 'index'])->name('pipeline');
    });

/* ---------- Area login (Breeze) ---------- */
Route::get('/dashboard', fn () => redirect()->route('admin.index'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* ---------- Admin ---------- */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Pengaturan situs: tiap bagian disimpan sendiri-sendiri
    Route::get('site', [SiteSettingController::class, 'edit'])->name('site.edit');
    Route::put('site/name', [SiteSettingController::class, 'updateName'])->name('site.name');
    Route::put('site/text/{field}', [SiteSettingController::class, 'updateText'])
        ->where('field', 'tagline|heart_statement|impact_statement|about_text')
        ->name('site.text');
    Route::post('site/image/{field}', [SiteSettingController::class, 'uploadImage'])
        ->where('field', 'logo|hero_image')
        ->name('site.image.upload');
    Route::delete('site/image/{field}', [SiteSettingController::class, 'destroyImage'])
        ->where('field', 'logo|hero_image')
        ->name('site.image.destroy');

    Route::resource('services', ServiceController::class)->except('show');
    Route::patch('services/{service}/move', [ServiceController::class, 'move'])->name('services.move');
    Route::resource('projects', ProjectController::class)->except(['show']);
    Route::patch('projects/{project}/publish', [ProjectController::class, 'togglePublish'])->name('projects.publish');
    Route::resource('clients', ClientController::class)->except('show');
    Route::patch('clients/{client}/move', [ClientController::class, 'move'])->name('clients.move');
});

require __DIR__.'/auth.php';