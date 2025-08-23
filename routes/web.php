<?php

use App\Http\Controllers\AbcController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ArrangementController;
use App\Http\Controllers\ArrangementFork;
use App\Http\Controllers\ArrangementTunebookController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ComposerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\RhythmController;
use App\Http\Controllers\SuccessController;
use App\Http\Controllers\TunebookController;
use App\Http\Controllers\TuneController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Stripe\Stripe;

Route::get('/', WelcomeController::class)->name('home');
Route::get('legal', LegalController::class)->name('legal');
Route::get('getstarted', function () {
    return Inertia::render('GetStarted');
})->name('getstarted');
Route::get('/abc', AbcController::class)->name('abc');

Route::get('admin-dashboard', AdminDashboardController::class)->middleware(['auth', 'verified'])->name('admin.dashboard');
Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::get('success', SuccessController::class)->middleware(['auth', 'verified'])->name('success');

Route::get('pricing', PricingController::class)->middleware(['auth', 'verified'])->name('pricing');

Route::get('checkout', CheckoutController::class)->middleware(['auth', 'verified'])->name('checkout');

Route::post('/lang/{lang}', LanguageController::class)->name('lang');

Route::post('like', LikeController::class)->middleware(['auth', 'verified'])->name('like');
Route::post('/arrangements/{arrangement}/fork', ArrangementFork::class)->middleware(['auth', 'verified'])->name('arrangements.fork');
Route::resource('arrangements', ArrangementController::class)->only([
    'show',
    'edit',
    'index',
    'store',
    'update',
    'destroy',
]);

Route::resource('comments', CommentController::class)->middleware(['auth', 'verified'])->only([
    'store',
    'update',
    'destroy',
]);

Route::post('/comments/{comment}/approve', [CommentController::class, 'approve'])->middleware(['auth', 'verified'])->name('comments.approve');
Route::post('/comments/{comment}/reject', [CommentController::class, 'reject'])->middleware(['auth', 'verified'])->name('comments.reject');

Route::resource('composers', ComposerController::class)->middleware(['auth', 'verified'])->only([
    'store',
    'update',
    'destroy',
]);

Route::resource('rhythms', RhythmController::class)->middleware(['auth', 'verified'])->only([
    'store',
    'update',
    'destroy',
]);

Route::resource('tunebooks', TunebookController::class)->only([
    'edit',
    'store',
    'update',
    'destroy',
    'index',
]);

Route::resource('arrangements.tunebooks', ArrangementTunebookController::class)->only([
    'store',
    'destroy',
]);

Route::resource('tunes', TuneController::class)->only([
    'show',
    'create',
    'store',
]);

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/google/redirect', [App\Http\Controllers\GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [App\Http\Controllers\GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');


// Route::get('/billing-portal', function (Request $request) {
//     return auth()->user()->redirectToBillingPortal(route('profile.pro'));
// })->middleware(['auth', 'verified'])->name('billing.portal');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
