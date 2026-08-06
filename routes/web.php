<?php

Route::get('/', function () {
    return auth()->check() ? redirect()->route('movies.index') : redirect()->route('login');
});

Route::get('/login', 'AuthController@showLogin')->middleware(['guest'])->name('login');
Route::post('/login', 'AuthController@login')->middleware(['guest', 'throttle:5,1'])->name('login.attempt');
Route::post('/locale', 'LocaleController@update')->name('locale.update');

Route::middleware('auth')->group(function () {
    Route::post('/logout', 'AuthController@logout')->name('logout');
    Route::get('/movies', 'MovieController@index')->name('movies.index');
    Route::get('/movies/search', 'MovieController@search')->name('movies.search');
    Route::get('/movies/{imdbId}', 'MovieController@show')->name('movies.show');
    Route::get('/favorites', 'FavoriteController@index')->name('favorites.index');
    Route::post('/favorites', 'FavoriteController@store')->name('favorites.store');
    Route::delete('/favorites/{imdbId}', 'FavoriteController@destroy')->name('favorites.destroy');
});
