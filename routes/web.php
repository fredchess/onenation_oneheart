<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\OrphanageController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\TestController;
use App\Http\Middleware\VerifyIsNotResponsable;

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
Route::group(['prefix' => 'admin', 'middleware' => ["auth", VerifyIsNotResponsable::class]], function () {
    Route::resource("orphanages", OrphanageController::class)->except(["update", "edit"]);
    Route::post("orphanages/bulk_delete", [OrphanageController::class, "multipleDestroy"])->name("orphanages.multipleDestroy");

    Route::resource("donations", DonationController::class);
    Route::post("donations/update_status", [DonationController::class, "update_status"])->name("donations.update_status");
    Route::post("donations/bulk_delete", [DonationController::class, "multipleDestroy"])->name("donations.multipleDestroy");

    Route::resource("users", UserController::class);
    Route::post("users/bulk_delete", [UserController::class, "multipleDestroy"])->name("users.multipleDestroy");

    Route::resource("partners", PartnerController::class);
    Route::post("partners/bulk_delete", [PartnerController::class, "multipleDestroy"])->name("partners.multipleDestroy");

    Route::resource("blogs", BlogController::class);
    Route::post("blogs/bulk_delete", [BlogController::class, "multipleDestroy"])->name("blogs.multipleDestroy");
    Route::post('blogs/add-image', [BlogController::class, 'addImage']);

    Route::resource("cities", CityController::class);
    Route::post("cities/bulk_delete", [CityController::class, "multipleDestroy"])->name("cities.multipleDestroy");

    Route::post('orphanages/images', [OrphanageController::class, 'storeImages'])->name('orphanage.storeImages');


    Route::resource('responsables', ResponsableController::class);
    Route::post("responsables/bulk_delete", [ResponsableController::class, "multipleDestroy"])->name("responsables.multipleDestroy");
});
// Route::group(['middleware' => 'auth'], function () {});

Route::group(['prefix' => 'admin', 'middleware' => ["auth"]], function () {
    Route::get("/", [AdminController::class, "index"])->name("admins.home");
    Route::resource('orphanages', OrphanageController::class)->only(['edit', 'update']);
});

Route::get('/orphanages/download', [OrphanageController::class, 'download'])
        ->middleware('auth')
        ->name('orphanages.download');

Route::get('/', [PageController::class, "home"])->name("public.home");
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

Auth::routes(['register' => false]);
