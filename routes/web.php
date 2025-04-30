<?php

use App\Http\Controllers\ArtworkController;  // Make sure you have this at the top of the file
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();


    // Add export route
    Route::get('artworks/data/export', [ArtworkController::class, 'exportToExcel'])->name('artworks.export');
});
