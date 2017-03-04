<?php

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

$pageCount = 15;
$pageNum = 1;

// stub some pages
while ($pageNum < $pageCount + 1) {
    Route::get('/page' . $pageNum, function () use ($pageNum) {
        return 'This is page #' . $pageNum;
    })->name('page' . $pageNum);

    $pageNum++;
}

Route::get('/', function () use ($pageCount) {
    return view('welcome', ['pageCount' => $pageCount]);
});

Auth::routes();

// admin routes
Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    Route::get('/', 'StatsController@index')->name('home');
    Route::get('/total', 'StatsController@total')->name('total');
    Route::get('/uri', 'StatsController@uri')->name('uri');
});
