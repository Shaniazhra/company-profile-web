<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AboutPhotoController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SiteTextController;
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
        Route::get('/about', [AboutController::class, 'index'])->name('about');

        $pages = \App\Http\Controllers\SitePagesController::class;
        Route::get('/projects', [$pages, 'projects'])->name('projects.index');
        Route::get('/projects/{project:slug}', [$pages, 'project'])->name('projects.show');
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
    ->middleware(['auth', 'verified.except_admin'])
    ->name('dashboard');

Route::middleware(['auth', 'verified.except_admin'])->group(function () {
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
    Route::put('/site/hero-focus', [SiteSettingController::class, 'updateHeroFocus'])->name('site.hero-focus');
    Route::put('site/text/{field}', [SiteSettingController::class, 'updateText'])
        ->where('field', 'tagline|heart_statement|impact_statement|about_text|story|vision|mission|bts_title|bts_text|gallery_title|gallery_text|cta_title|cta_text|about_label|about_title|story_title|cta_label')
        ->name('site.text');
    // Save all texts of a group at once (English only)
    Route::put('site/texts/home', [SiteTextController::class, 'home'])->name('site.texts.home');
    Route::put('site/texts/about', [SiteTextController::class, 'about'])->name('site.texts.about');
    Route::post('site/image/{field}', [SiteSettingController::class, 'uploadImage'])
        ->where('field', 'logo|hero_image|about_image|about_image_2|about_image_3|about_image_4|bts_poster')
        ->name('site.image.upload');
    Route::delete('site/image/{field}', [SiteSettingController::class, 'destroyImage'])
        ->where('field', 'logo|hero_image|about_image|about_image_2|about_image_3|about_image_4|bts_poster')
        ->name('site.image.destroy');
    Route::put('site/image/{field}/focus', [SiteSettingController::class, 'updateFocus'])
        ->where('field', 'logo|hero_image|about_image|about_image_2|about_image_3|about_image_4|bts_poster')
        ->name('site.image.focus');

    // Behind-the-scenes video, and showing or hiding whole sections of the About page
    Route::post('site/video', [SiteSettingController::class, 'uploadVideo'])->name('site.video.upload');
    Route::delete('site/video', [SiteSettingController::class, 'destroyVideo'])->name('site.video.destroy');
    Route::put('site/section/{section}', [SiteSettingController::class, 'toggleSection'])
        ->where('section', 'bts|gallery')
        ->name('site.section');

    // Gallery photos on the About page
    Route::match(['put', 'post', 'patch'], 'about-photos-order', [AboutPhotoController::class, 'reorder'])->name('about-photos.order');
    Route::post('about-photos', [AboutPhotoController::class, 'store'])->name('about-photos.store');
    Route::put('about-photos/{photo}', [AboutPhotoController::class, 'update'])->name('about-photos.update');
    Route::patch('about-photos/{photo}/move', [AboutPhotoController::class, 'move'])->name('about-photos.move');
    Route::delete('about-photos/{photo}', [AboutPhotoController::class, 'destroy'])->name('about-photos.destroy');

    // Drag and drop order of the Services list
    Route::put('services-order', [ServiceController::class, 'reorder'])->name('services.order');
    Route::resource('services', ServiceController::class)->except('show');
    Route::patch('services/{service}/move', [ServiceController::class, 'move'])->name('services.move');
    // Hero photo of the public Projects page (set from the Projects list)
    Route::put('projects-hero', [ProjectController::class, 'updateHero'])->name('projects.hero.update');
    Route::put('projects-titles', [ProjectController::class, 'updateTitles'])->name('projects.titles');
    Route::delete('projects-hero', [ProjectController::class, 'destroyHero'])->name('projects.hero.destroy');
    Route::resource('projects', ProjectController::class)->except(['show']);
    Route::patch('projects/{project}/publish', [ProjectController::class, 'togglePublish'])->name('projects.publish');
    // Drag and drop order of the Clients list
    Route::match(['put', 'post', 'patch'], 'clients-order', [ClientController::class, 'reorder'])->name('clients.order');
    Route::resource('clients', ClientController::class)->except('show');
    Route::patch('clients/{client}/move', [ClientController::class, 'move'])->name('clients.move');

    // Team page: hero photo, and the order of the people (drag and drop in the Team list)
    Route::put('team-hero', [\App\Http\Controllers\Admin\CrewMemberController::class, 'updateHero'])->name('team.hero.update');
    Route::delete('team-hero', [\App\Http\Controllers\Admin\CrewMemberController::class, 'destroyHero'])->name('team.hero.destroy');
    Route::put('team-order', [\App\Http\Controllers\Admin\CrewMemberController::class, 'reorder'])->name('team.order');
    // Team divisions (Animation, Production, ...)
    Route::post('team-divisions', [\App\Http\Controllers\Admin\CrewMemberController::class, 'storeDivision'])->name('team.divisions.store');
    Route::put('team-divisions-order', [\App\Http\Controllers\Admin\CrewMemberController::class, 'reorderDivisions'])->name('team.divisions.order');
    Route::put('team-divisions/{division}', [\App\Http\Controllers\Admin\CrewMemberController::class, 'updateDivision'])->name('team.divisions.update');
    Route::delete('team-divisions/{division}', [\App\Http\Controllers\Admin\CrewMemberController::class, 'destroyDivision'])->name('team.divisions.destroy');
    Route::put('contacts-order', [\App\Http\Controllers\Admin\ContactPersonController::class, 'reorder'])->name('contacts.order');

    // Drag and drop order of the FAQ list
    Route::match(['put', 'post', 'patch'], 'faqs-order', [\App\Http\Controllers\Admin\FaqController::class, 'reorder'])->name('faqs.order');

    foreach ([
        'team' => \App\Http\Controllers\Admin\CrewMemberController::class,
        'faqs' => \App\Http\Controllers\Admin\FaqController::class,
        'process' => \App\Http\Controllers\Admin\ProcessStageController::class,
        'contacts' => \App\Http\Controllers\Admin\ContactPersonController::class,
        'highlights' => \App\Http\Controllers\Admin\HighlightController::class,
    ] as $name => $controller) {
        Route::resource($name, $controller)->except('show')->parameters([$name => 'item']);
        Route::patch($name.'/{item}/move', [$controller, 'move'])->name($name.'.move');
    }

    Route::resource('inquiries', \App\Http\Controllers\Admin\InquiryController::class)->only(['index', 'show', 'destroy'])->parameters(['inquiries' => 'item']);
    Route::patch('inquiries/{item}/read', [\App\Http\Controllers\Admin\InquiryController::class, 'toggleRead'])->name('inquiries.read');
});

require __DIR__.'/auth.php';