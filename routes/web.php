<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::get('/',[PageController::class, "home"])->name("public.home");
Route::get('/about', [PageController::class, "about"])->name("public.about");
Route::get('/search', [PageController::class, "search"])->name("public.search");
Route::get('/contact', [PageController::class, "contact"])->name("public.contact");
Route::get('/joinus', [PageController::class, "joinus"])->name("public.joinus");
Route::post('/joinus', [PageController::class, "joinus_register"])->name("public.joinus.register");

Route::get('/orphelinats', [PageController::class, "orphanages"])->name("public.orphanages");
Route::get('/orphelinats/{orphanage_slug}', [PageController::class, "orphanages_detail"])->name("public.orphanages.details");

Route::get('/blog', [PageController::class, "blog"])->name("public.blog");
Route::get('/blog/{blog_slug}', [PageController::class, "blog_detail"])->name("public.blog.details");

Route::post('/donation', [DonationController::class, "store"])->name("public.donation");
Route::post('/donation/callback', [DonationController::class, "callback"])->name('donation.callback');

Route::get('/test', [TestController::class, 'List']);

Route::post("callback/dvXQEdsFNNCcfTYCrvGY", [DonationController::class, 'callback_dvXQEdsFNNCcfTYCrvGY'])->name('callback');

Auth::routes(['register' => false, 'login' => false]);

Route::get('login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');
