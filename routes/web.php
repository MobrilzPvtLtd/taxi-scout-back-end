<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
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


/*
 * These routes use the root namespace 'App\Http\Controllers\Web'.
 */
Route::namespace('Web')->group(function () {

    // All the folder based web routes
    include_route_files('web');


    // Route::get('/', 'FrontPageController@index')->name('index');
    Route::get('/driverpage', 'FrontPageController@driverp')->name('driverpage');
    Route::get('/howdriving', 'FrontPageController@howdrive')->name('howdriving');
    Route::get('/driverrequirements', 'FrontPageController@driverrequirement')->name('driverrequirements');
    Route::get('/safety', 'FrontPageController@safetypage')->name('safety');
    Route::get('/serviceareas', 'FrontPageController@serviceareaspage')->name('serviceareas');
    Route::get('/compliance', 'FrontPageController@complaincepage')->name('complaince');
    Route::get('/privacy', 'FrontPageController@privacypage')->name('privacy');
    Route::get('/terms', 'FrontPageController@termspage')->name('terms');
    Route::get('/dmv', 'FrontPageController@dmvpage')->name('dmv');
    Route::get('/contactus', 'FrontPageController@contactuspage')->name('contactus');
    Route::post('/contactussendmail','FrontPageController@contactussendmailadd')->name('contactussendmail');


    Route::get('mercadopago-checkout',function(){
        return view('mercadopago.checkout');
    });

    Route::get('mercadopago-success','MercadopagoController@success');

    Route::view("success",'success');
    Route::view("failure",'failure');
    Route::view("pending",'pending');


    // storage link command code
    Route::get('/symlink', function(){
        $publicStoragePath = public_path('storage');
        $storagePath = storage_path('app/public');

        if (!file_exists($publicStoragePath)) {
            if (symlink($storagePath, $publicStoragePath)) {
                $this->info('Symbolic link created successfully.');
            } else {
                $this->error('Failed to create symbolic link.');
            }
        } else {
            $this->info('Symbolic link already exists.');
        }
    });

    // Website home route
    //Route::get('/', 'HomeController@index')->name('home');
    Route::get('/run-schedule', function () {
        Artisan::call('scheduledRunOnLive:run');
        $output = Artisan::output();
        return response()->json(['message' => 'Scheduled commands executed.', 'output' => $output]);
    });
});

