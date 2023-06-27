<?php

use Illuminate\Support\Facades\Route;

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

/**
 * @hideFromAPIDocumentation
 */


$prefix = config('scribe.laravel.docs_url', '/');
$middleware = config('scribe.laravel.middleware', []);

Route::middleware($middleware)->group(function () use ($prefix) {
    Route::view($prefix, 'scribe.index')->name('scribe');

    Route::get("$prefix.postman", function () {
        return new JsonResponse(Storage::disk('local')->get('scribe/collection.json'), json: true);
    })->name('scribe.postman');

    Route::get("$prefix.openapi", function () {
        return response()->file(Storage::disk('local')->path('scribe/openapi.yaml'));
    })->name('scribe.openapi');
});