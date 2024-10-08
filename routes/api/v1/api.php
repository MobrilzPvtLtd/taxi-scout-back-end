<?php

/*
|--------------------------------------------------------------------------
| Common API Routes
|--------------------------------------------------------------------------
|
| These routes are prefixed with 'api/v1'.
| These routes use the root namespace 'App\Http\Controllers\Api\V1'.
|
 */

/*
         * Root namespace 'App\Http\Controllers\Api\V1\Common'.
    */
Route::namespace('Common')->group(function () {

    // List all the cities.
    Route::get('cities', 'CityController@index');

    // Get Cities by State
    Route::get('cities/by/state/{state_id}', 'CityController@byState');

    // Get the city by its id.
    Route::get('cities/{city}', 'CityController@show');

    // List all the states.
    Route::get('states', 'StateController@index');

    // Get the state by its id.
    Route::get('states/{state}', 'StateController@show');

    // Get all the countries.
    Route::get('countries', 'CountryController@index');
    // Get Language translation for mobile
    Route::get('translation/get', 'TranslationController@index');
    Route::get('translation-flutter/get', 'TranslationController@flutterTrnaslation');

    // Get all the ServiceLocation.
    Route::get('servicelocation', 'ServiceLocationController@index');

     // List all the blogs.
     Route::get('blogs', 'BlogController@index');
     Route::get('blog-details/{slug}', 'BlogController@blogDetails');

     Route::get('gallery', 'StoreFrontController@gallery');
     Route::get('faq', 'StoreFrontController@faq');
     Route::get('our-team', 'StoreFrontController@ourTeam');
     Route::get('our-partner', 'StoreFrontController@ourPartner');
     Route::post('contact', 'StoreFrontController@contact');
});

Route::namespace('Company')->group(function () {
    // List all the cities.
    Route::post('compnayregister', 'AdminController@store');
	 Route::get('getroll', 'AdminController@create');

});
