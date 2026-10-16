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

        $pages = \App\Http\Controllers\SitePagesController::class;
        Route::get('/services', [$pages, 'services'])->name('services.index');
        Route::get('/services/{service}', [$pages, 'service'])->name('services.show');
        Route::get('/projects', [$pages, 'projects'])->name('projects.index');
        Route::get('/projects/{project}', [$pages, 'project'])->name('projects.show');
        Route::get('/process', [$pages, 'process'])->name('process');
        Route::get('/team', [$pages, 'team'])->name('team');
        Route::get('/faq', [$pages, 'faq'])->name('faq');
        Route::get('/contact', [$pages, 'contact'])->name('contact');
        Route::post('/contact', [$pages, 'contactStore'])->middleware('throttle:5,1')->name('contact.store');

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

    foreach ([
        'team' => \App\Http\Controllers\Admin\CrewMemberController::class,
        'faqs' => \App\Http\Controllers\Admin\FaqController::class,
        'process' => \App\Http\Controllers\Admin\ProcessStageController::class,
        'contacts' => \App\Http\Controllers\Admin\ContactPersonController::class,
    ] as $name => $controller) {
        Route::resource($name, $controller)->except('show')->parameters([$name => 'item']);
        Route::patch($name.'/{item}/move', [$controller, 'move'])->name($name.'.move');
    }

    Route::resource('inquiries', \App\Http\Controllers\Admin\InquiryController::class)->only(['index', 'show', 'destroy'])->parameters(['inquiries' => 'item']);
    Route::patch('inquiries/{item}/read', [\App\Http\Controllers\Admin\InquiryController::class, 'toggleRead'])->name('inquiries.read');
});

require __DIR__.'/auth.php';