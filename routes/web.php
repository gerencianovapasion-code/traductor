<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TranslatorController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------- Public site
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('/features', [PageController::class, 'features'])->name('features');
Route::get('/legal/{doc}', [PageController::class, 'legal'])->name('legal');

// The translation app (works for guests; quotas/cloud engine apply once logged in)
Route::get('/translate', [TranslatorController::class, 'index'])->name('translate');

// Translation API (used by the PWA frontend)
Route::post('/api/translate', [TranslatorController::class, 'translate'])->name('api.translate');
Route::post('/api/detect', [TranslatorController::class, 'detect'])->name('api.detect');
Route::post('/api/usage', [TranslatorController::class, 'logUsage'])->name('api.usage');

// Locale switch + SEO
Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// ---------------------------------------------------------------- Guest auth
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// ---------------------------------------------------------------- Authenticated
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Memberships
    Route::get('/membership', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/membership/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/membership-cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

    // Affiliate program
    Route::get('/affiliate', [AffiliateController::class, 'index'])->name('affiliate.index');
    Route::post('/affiliate/payout', [AffiliateController::class, 'requestPayout'])->name('affiliate.payout');
});

// ---------------------------------------------------------------- Admin panel
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');

    Route::get('/plans', [\App\Http\Controllers\Admin\PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/create', [\App\Http\Controllers\Admin\PlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [\App\Http\Controllers\Admin\PlanController::class, 'store'])->name('plans.store');
    Route::get('/plans/{plan}/edit', [\App\Http\Controllers\Admin\PlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [\App\Http\Controllers\Admin\PlanController::class, 'update'])->name('plans.update');

    Route::get('/languages', [\App\Http\Controllers\Admin\LanguageController::class, 'index'])->name('languages.index');
    Route::put('/languages/{language}', [\App\Http\Controllers\Admin\LanguageController::class, 'update'])->name('languages.update');

    Route::get('/affiliates', [\App\Http\Controllers\Admin\AffiliateController::class, 'index'])->name('affiliates.index');
    Route::post('/affiliates/commissions/{commission}/approve', [\App\Http\Controllers\Admin\AffiliateController::class, 'approveCommission'])->name('affiliates.commission.approve');
    Route::post('/affiliates/commissions/{commission}/reject', [\App\Http\Controllers\Admin\AffiliateController::class, 'rejectCommission'])->name('affiliates.commission.reject');
    Route::post('/affiliates/payouts/{payout}/pay', [\App\Http\Controllers\Admin\AffiliateController::class, 'payPayout'])->name('affiliates.payout.pay');

    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
});
