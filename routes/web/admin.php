<?php

/*
|--------------------------------------------------------------------------
| SPA Auth Routes
|--------------------------------------------------------------------------
|
| These routes are prefixed with '/'.
| These routes use the root namespace 'App\Http\Controllers\Web'.
|
 */

use App\Base\Constants\Auth\Role;
use App\Http\Controllers\PayPalController;


/*
 * These routes are used for web authentication.
 *
 * Route prefix 'api/spa'.
 * Root namespace 'App\Http\Controllers\Web\Admin'.
 */

/**
 * Temporary dummy route for testing SPA.
 */


Route::middleware('guest')->namespace('Admin')->group(function () {

    // Get admin-login form
    Route::get('/', 'AdminViewController@viewLogin');

    Route::get('company-login', 'FleetOwnerController@viewLogin');

    Route::get('login/{provider}', 'AdminViewController@redirectToProvider');

    Route::get('login/callback/{provider}', 'AdminViewController@handleProviderCallback');
});

Route::middleware('auth:web')->group(function () {

        //cMS
    Route::group(['prefix' => 'cms'], function () {

            Route::post('/frontpagecmsadd','FrontPageController@frontpageadd')->name('frontpagecmsadd');
            Route::get('/frontpagecms', 'FrontPageController@frontpage')->name('frontpagecms');
            Route::post('/safetypageadd','FrontPageController@safetypagecmsadd')->name('safetypageadd');
            Route::get('/safetypagecms', 'FrontPageController@safetypagecms')->name('safetypagecms');
            Route::post('/servicepageadd','FrontPageController@servicepagecmsadd')->name('servicepageadd');
            Route::get('/servicepage', 'FrontPageController@servicepagecms')->name('servicepage');
            Route::post('/privacypageadd','FrontPageController@privacypagecmsadd')->name('privacypageadd');
            Route::get('/privacypagecms', 'FrontPageController@privacypagecms')->name('privacypagecms');
            Route::post('/dmvpageadd','FrontPageController@dmvpagecmsadd')->name('dmvpageadd');
            Route::get('/dmvpagecms', 'FrontPageController@dmvpagecms')->name('dmvpagecms');
            Route::post('/complaincepageadd','FrontPageController@complaincepagecmsadd')->name('complaincepageadd');
            Route::get('/complaincepagecms', 'FrontPageController@complaincepagecms')->name('complaincepagecms');
            Route::post('/termspageadd','FrontPageController@termspagecmsadd')->name('termspageadd');
            Route::get('/termspagecms', 'FrontPageController@termspagecms')->name('termspagecms');
            Route::post('/drreqpageadd','FrontPageController@drreqpagecmsadd')->name('drreqpageadd');
            Route::get('/drreqpagecms', 'FrontPageController@drreqpagecms')->name('drreqpagecms');
            Route::post('/applydriverpageadd','FrontPageController@applydriverpagecmsadd')->name('applydriverpageadd');
            Route::get('/applydriverpagecms', 'FrontPageController@applydriverpagecms')->name('applydriverpagecms');
            Route::post('/howdriverpageadd','FrontPageController@howdriverpagecmsadd')->name('howdriverpageadd');
            Route::get('/howdriverpagecms', 'FrontPageController@howdriverpagecms')->name('howdriverpagecms');
            Route::post('/contactpageadd','FrontPageController@contactpagecmsadd')->name('contactpageadd');
            Route::get('/contactpagecms', 'FrontPageController@contactpagecms')->name('contactpagecms');
            Route::post('/playstorepageadd','FrontPageController@playstorepagecmsadd')->name('playstorepageadd');
            Route::get('/playstorepagecms', 'FrontPageController@playstorepagecms')->name('playstorepagecms');
            Route::post('/footerpageadd','FrontPageController@footerpagecmsadd')->name('footerpageadd');
            Route::get('/footerpagecms', 'FrontPageController@footerpagecms')->name('footerpagecms');
            Route::post('/colorthemepageadd','FrontPageController@colorthemepagecmsadd')->name('colorthemepageadd');
            Route::get('/colorthemepagecms', 'FrontPageController@colorthemepagecms')->name('colorthemepagecms');

    });

    Route::namespace('Admin')->group(function () {
        Route::get('dispatcher-request','AdminViewController@dispatchRequest');
    // Owner Management (Company Management)
    Route::group(['prefix' => 'owners'], function () {
        Route::get('/', 'OwnerController@index')->name('ownerView');
        Route::get('/fetch', 'OwnerController@getAllOwner');
        Route::get('by_area/{area}', 'OwnerController@index')->name('ownerByArea');
        Route::get('by_area/fetch/{area}', 'OwnerController@getAllOwner');
        // Route::get('/create/{area}', 'OwnerController@create');
        Route::get('/create', 'AdminController@create');
        Route::post('store', 'OwnerController@store');
        // Route::get('/{owner}', 'OwnerController@getById');
        Route::get('edit/{owner}', 'AdminController@getById');
        Route::post('update/{owner}', 'OwnerController@update');
        Route::get('toggle_status/{owner}', 'OwnerController@toggleStatus');
        Route::get('toggle_approve/{owner}', 'OwnerController@toggleApprove');
        Route::get('delete/{owner}', 'OwnerController@delete');
        Route::get('get/owner', 'OwnerController@getOwnerByArea')->name('getOwnerByArea');
        Route::get('document/view/{owner}', 'OwnerDocumentController@index')->name('ownerDocumentView');
        Route::get('upload/document/{owner}/{needed_document}', 'OwnerDocumentController@documentUploadView');
        Route::post('upload/document/{owner}/{needed_document}', 'OwnerDocumentController@uploadDocument')->name('updateOwnerDocument');
        Route::post('approve/documents', 'OwnerDocumentController@approveOwnerDocument')->name('approveOwnerDocument');
        Route::get('payment-history/{owner}', 'OwnerController@OwnerPaymentHistory');
        Route::post('payment-history/{owner}', 'OwnerController@StoreOwnerPaymentHistory');
    });

    // Fleet CRUD
    Route::group(['prefix' => 'fleets'], function () {
        Route::get('/', 'FleetController@index')->name('viewFleet');
        Route::get('/fetch', 'FleetController@fetch')->name('fetchFleet');
        Route::get('/create', 'FleetController@create')->name('createFleet');
        Route::post('store', 'FleetController@store')->name('storeFleet');
        Route::get('edit/{fleet}', 'FleetController@getById')->name('editFleet');
        Route::post('update/{fleet}', 'FleetController@update')->name('updateFleet');
        Route::get('toggle_status/{fleet}', 'FleetController@toggleStatus')->name('toggleFleetStatus');
        Route::get('toggle_approve/{fleet}', 'FleetController@toggleApprove')->name('toggleFleetApprove');
        Route::get('delete/{fleet}', 'FleetController@delete')->name('deleteFleet');
        Route::post('update/decline/reason', 'FleetController@updateFleetDeclineReason')->name('updateFleetDeclineReason');
        Route::get('assign_driver/{fleet}', 'FleetController@assignDriverView')->name('assignFleetToDriverView');
        Route::post('assign_driver/{fleet}', 'FleetController@assignDriver')->name('assignFleetToDriver');
        Route::get('document/view/{fleet}', 'FleetDocumentController@index')->name('FleetDocumentView');
        Route::get('upload/document/{fleet}/{needed_document}', 'FleetDocumentController@documentUploadView');
        Route::post('upload/document/{fleet}/{needed_document}', 'FleetDocumentController@uploadDocument')->name('updateFleetDocument');
        Route::post('approve/documents', 'FleetDocumentController@approveFleetDocument')->name('approveFleetDocument');

    });

    // Driver Management
    Route::group(['prefix' => 'company/drivers','namespace'=>'Company'], function () {
        // prefix('drivers')->group(function () {
        Route::get('/', 'DriverController@index')->name('companyDriverView');
        Route::get('/fetch', 'DriverController@getAllDrivers');
        Route::get('/create', 'DriverController@create');
        Route::post('store', 'DriverController@store');
        Route::get('/{driver}', 'DriverController@getById');
        Route::get('approve/{driver}', 'DriverController@approveDriver');
        Route::post('update/{driver}', 'DriverController@update');
        Route::get('toggle_status/{driver}', 'DriverController@toggleStatus');
        Route::get('toggle_approve/{driver}', 'DriverController@toggleApprove');
        Route::get('toggle_available/{driver}', 'DriverController@toggleAvailable');
        Route::get('delete/{driver}', 'DriverController@delete');
        Route::get('document/view/{driver}', 'DriverDocumentController@index');
        Route::get('upload/document/{driver}/{needed_document}', 'DriverDocumentController@documentUploadView');
        Route::post('upload/document/{driver}/{needed_document}', 'DriverDocumentController@uploadDocument');
        Route::post('approve/documents', 'DriverDocumentController@approveDriverDocument')->name('approveCompanyDriverDocument');
        Route::post('update/decline/reason', 'DriverController@UpdateDriverDeclineReason')->name('UpdateDriverDeclineReason');
        Route::get('get/carmodel', 'DriverController@getCarModel')->name('getCarModel');
        Route::get('profile/{driver}', 'DriverController@profile');
        Route::get('hire/view', 'DriverController@hireDriverView')->name('hireDriverView');
        Route::post('hire', 'DriverController@hireDriver')->name('hireDriver');
        Route::get('vehicle/privileges/{driver}','DriverController@fleetPrivilegeView')->name('fleetPrivilegeView');
        Route::post('store/vehicle/privileges/{driver}','DriverController@storePrivilegedVehicle')->name('storePrivilegedVehicle');
        Route::get('unlink/fleet/{driver}/{vehicle}','DriverController@unlinkVehicle')->name('unlinkVehicle');

        // Route::get('request-list/{driver}', 'DriverController@DriverTripRequestIndex');
        // Route::get('request-list/{driver}/fetch', 'DriverController@DriverTripRequest');
        // Route::get('payment-history/{driver}', 'DriverController@DriverPaymentHistory');
        // Route::post('payment-history/{driver}', 'DriverController@StoreDriverPaymentHistory');


    });

});
});



Route::middleware('guest')->namespace('Dispatcher')->group(function () {
    // Get admin-login form
    Route::get('dispatch-login', 'DispatcherController@loginView');
});

Route::middleware('guest')->namespace('DeliveryDispatcher')->group(function () {
    // Get admin-login form
    Route::get('dispatch-delivery-login', 'DeliveryDispatcherController@loginView');
});


Route::namespace('Admin')->group(function () {
    Route::get('track/request/{request}', 'AdminViewController@trackTripDetails');
});


Route::middleware('auth:web')->group(function () {
    Route::post('logout', function () {
        auth('web')->logout();
        request()->session()->invalidate();
        return redirect('/');
    });
    // Masters Crud
    // Route::middleware(role_middleware(Role::webPanelLoginRoles()))->group(function () {
        /**
         * Vehicle Types
         */
        Route::namespace('Admin')->group(function () {
            Route::get('view-services', 'AdminViewController@viewServices');
            Route::prefix('types')->group(function () {
                Route::get('/', 'VehicleTypeController@index');
                Route::get('/fetch', 'VehicleTypeController@getAllTypes');
                Route::get('by/admin', 'VehicleTypeController@byAdmin');
                Route::get('/create', 'VehicleTypeController@create');
                Route::post('/store', 'VehicleTypeController@store');
                Route::get('edit/{id}', 'VehicleTypeController@edit');
                Route::post('/update/{vehicle_type}', 'VehicleTypeController@update');
                Route::get('toggle_status/{vehicle_type}', 'VehicleTypeController@toggleStatus');
                Route::get('/delete/{vehicle_type}', 'VehicleTypeController@delete');
            });
        });
    // });

    Route::namespace('Admin')->group(function () {
        // Change Locale
        Route::get('/change/lang/{lang}', 'AdminViewController@changeLocale')->name('changeLocale');

        Route::get('dashboard', 'DashboardController@dashboard');
        // Route::get('dashboard', 'AdminViewController@dashboard');
        Route::get('/admin_dashboard', 'AdminViewController@viewTestDashboard')->name('admin_dashboard');
        Route::get('/driver_profile_dashboard', 'AdminViewController@driverPrfDashboard')->name('driver_profile_dashboard');
        Route::get('/driver_profile_dashboard_view/{driver}', 'AdminViewController@driverPrfDashboardView');

        Route::group(['prefix' => 'company',  'middleware' => 'permission:view-companies'], function () {
            // prefix('company')->group(function () {
            Route::get('/', 'CompanyController@index');
            Route::get('/fetch', 'CompanyController@getAllCompany');
            Route::get('by/admin', 'CompanyController@byAdmin');
            Route::get('/create', 'CompanyController@create');
            Route::post('store', 'CompanyController@store');
            Route::get('edit/{company}', 'CompanyController@getById');
            Route::post('update/{company}', 'CompanyController@update');
            Route::get('toggle_status/{company}', 'CompanyController@toggleStatus');
            Route::get('delete/{company}', 'CompanyController@delete');
        });

//drivers

    Route::group(['prefix' => 'drivers'], function () {
        // prefix('drivers')->group(function () {
        Route::get('/', 'DriverController@index');
        Route::get('/fetch/approved', 'DriverController@getApprovedDrivers');

        Route::get('/waiting-for-approval', 'DriverController@approvalPending');
        // Route::get('/fetch', 'DriverController@getAllDrivers');
        Route::get('/fetch/approval-pending-drivers', 'DriverController@getApprovalPendingDrivers');
        Route::get('/fetch/driver-ratings', 'DriverController@fetchDriverRatings');

        Route::get('/create', 'DriverController@create');
        Route::post('store', 'DriverController@store');
        Route::get('/{driver}', 'DriverController@getById');
        Route::get('request-list/{driver}', 'DriverController@DriverTripRequestIndex');
        Route::get('request-list/{driver}/fetch', 'DriverController@DriverTripRequest');
        Route::get('payment-history/{driver}', 'DriverController@DriverPaymentHistory');
        Route::post('payment-history/{driver}', 'DriverController@StoreDriverPaymentHistory');
        Route::post('update/{driver}', 'DriverController@update');
        Route::get('toggle_status/{driver}', 'DriverController@toggleStatus');
        Route::get('toggle_approve/{driver}/{approval_status}', 'DriverController@toggleApprove');
        Route::get('toggle_available/{driver}', 'DriverController@toggleAvailable');
        Route::get('delete/{driver}', 'DriverController@delete');
        Route::get('document/view/{driver}', 'DriverDocumentController@index');
        Route::get('upload/document/{driver}/{needed_document}', 'DriverDocumentController@documentUploadView');
        Route::post('upload/document/{driver}/{needed_document}', 'DriverDocumentController@uploadDocument');
        Route::post('approve/documents', 'DriverDocumentController@approveDriverDocument')->name('approveDriverDocument');
        Route::get('get/carmake', 'DriverController@getCarMake')->name('getCarMake');
        Route::get('get/carmodel', 'DriverController@getCarModel')->name('getCarModel');
        Route::post('update/decline/reason', 'DriverController@UpdateDriverDeclineReason')->name('UpdateDriverDeclineReason');
        Route::get('get/type', 'DriverController@getType')->name('getType');

        });

        Route::group(['prefix'=>'driver-ratings'], function () {
             Route::get('/','DriverController@driverRatings');
             Route::get('/view/{driver}','DriverController@driverRatingView');
        });
         Route::group(['prefix'=>'withdrawal-requests-lists'], function () {
             Route::get('/','DriverController@withdrawalRequestsList');
             Route::get('/view/{driver}','DriverController@withdrawalRequestDetail');
             Route::get('/approve/{wallet_withdrawal_request}','DriverController@approveWithdrawalRequest');
             Route::get('/decline/{wallet_withdrawal_request}','DriverController@declineWithdrawalRequest');
       Route::get('/negative_balance_drivers','DriverController@NeagtiveBalanceDrivers');
       Route::get('fetch/negative-balance-drivers', 'DriverController@NegativeBalanceFetch');
        });

//Fleet drivers

    Route::group(['prefix' => 'fleet-drivers'], function () {
        // prefix('drivers')->group(function () {
        Route::get('/', 'FleetDriverController@index');
        Route::get('/fetch/approved', 'FleetDriverController@getApprovedFleetDrivers');

        Route::get('/waiting-for-approval', 'FleetDriverController@approvalPending');
        // Route::get('/fetch', 'DriverController@getAllDrivers');
        Route::get('/fetch/approval-pending-drivers', 'FleetDriverController@getApprovalPendingFleetDrivers');
        Route::get('/fetch/driver-ratings', 'FleetDriverController@fetchFleetDriverRatings');

        Route::get('/create', 'FleetDriverController@create');
        Route::post('store', 'FleetDriverController@store');
        Route::get('/{driver}', 'FleetDriverController@getById');
        Route::get('request-list/{driver}', 'FleetDriverController@DriverTripRequestIndex');
        Route::get('request-list/{driver}/fetch', 'FleetDriverController@FleetDriverTripRequest');
        Route::get('payment-history/{driver}', 'FleetDriverController@FleetDriverPaymentHistory');
        Route::post('payment-history/{driver}', 'FleetDriverController@StoreFleetDriverPaymentHistory');
        Route::post('update/{driver}', 'FleetDriverController@update');
        Route::get('toggle_status/{driver}', 'FleetDriverController@toggleStatus');
        Route::get('toggle_approve/{driver}/{approval_status}', 'FleetDriverController@toggleApprove');
        Route::get('toggle_available/{driver}', 'FleetDriverController@toggleAvailable');
        Route::get('delete/{driver}', 'FleetDriverController@delete');
        Route::get('document/view/{driver}', 'FleetDriverDocumentController@index');
        Route::get('upload/document/{driver}/{needed_document}', 'FleetDriverDocumentController@documentUploadView');
        Route::post('upload/document/{driver}/{needed_document}', 'FleetDriverDocumentController@uploadDocument');
        Route::post('approve/documents', 'FleetDriverDocumentController@approveFleetDriverDocument')->name('approveFleetDriverDocument');
        Route::get('get/carmodel', 'FleetDriverController@getCarModel')->name('getCarModel');
        Route::post('update/decline/reason', 'FleetDriverController@UpdateDriverDeclineReason')->name('UpdateFleetDriverDeclineReason');

        });
        Route::group(['prefix' => 'admins',  'middleware' => 'permission:admin'], function () {
            // prefix('admins')->group(function () {
            // Route::get('/', 'AdminController@index');
            // Route::get('/fetch', 'AdminController@getAllAdmin');
            // Route::get('/create', 'AdminController@create');
            Route::post('store', 'AdminController@store');
            // Route::get('edit/{admin}', 'AdminController@getById');
            Route::post('update/{admin}', 'AdminController@update');
            Route::get('toggle_status/{user}', 'AdminController@toggleStatus');
            Route::get('delete/{user}', 'AdminController@delete');
            Route::get('approve/{user}', 'AdminController@approveUser'); // taxi company approve by superadmin
            Route::get('profile/{user}', 'AdminController@viewProfile');
            Route::post('profile/update/{user}', 'AdminController@updateProfile');
        });
        // Zone CRUD
        Route::group(['prefix' => 'zone',  'middleware' => 'permission:view-zone'], function () {
            // prefix('zone')->group(function () {
            Route::get('/', 'ZoneController@index');
            Route::get('/fetch', 'ZoneController@getAllZone');
            Route::get('/mapview/{id}', 'ZoneController@zoneMapView');
            Route::get('/create', 'ZoneController@create');
            Route::get('/edit/{id}', 'ZoneController@zoneEdit');
            Route::post('update/{zone}', 'ZoneController@update');
            Route::get('/assigned/types/{zone}', 'ZoneController@assignTypesView');
            Route::get('/assign/types/{zone}', 'ZoneController@assignTypesCreateView');
            Route::post('/assign/types/{zone}', 'ZoneController@assignTypesStore');
            Route::get('/types/edit/{zone_type}', 'ZoneController@typesEditPriceView');
            Route::post('/types/edit/{zone_type}', 'ZoneController@typesPriceUpdate')->name('updateTypePrice');
            Route::post('store', 'ZoneController@store');
            Route::get('/{id}', 'ZoneController@getById');
            Route::get('/delete/{zone}', 'ZoneController@delete');
            Route::get('/toggle_status/{zone}', 'ZoneController@toggleZoneStatus');
            Route::get('/types/toggleStatus/{zone_type}', 'ZoneController@toggleStatus');
            Route::get('/types/delete/{zone_type}', 'ZoneController@deleteZoneType');
            Route::get('/surge/{zone}', 'ZoneController@surgeView');
            Route::post('/surge/update/{zone}', 'ZoneController@updateSurgePrice');
            Route::get('/set/default/{zone_type}', 'ZoneController@setDefaultType');
            Route::get('/coords/by_keyword/{keyword}', 'ZoneController@getCoordsByKeyword')->name('getCoordsByKeyword');
            Route::get('/search/city', 'ZoneController@getCityBySearch')->name('getCityBySearch');

             Route::get('/types/zone_package_price/index/{zone_type}', 'ZoneController@packageIndex');

             Route::get('/types/zone_package_price/{zone_type}', 'ZoneController@packageCreate');
             Route::post('/types/zone_package_price/store/{zone_type}', 'ZoneController@packageStore');
             Route::get('/types/zone_package_price/edit/{package}', 'ZoneController@packageEdit');
             Route::post('/types/zone_package_price/update/{package}', 'ZoneController@packageUpdate');
             Route::get('/types/zone_package_price/delete/{package}', 'ZoneController@packageDelete');
             Route::get('/types/zone_package_price/toggleStatus/{package}', 'ZoneController@PackagetoggleStatus');
        });

                // Zone CRUD
        Route::group(['prefix' => 'airport',  'middleware' => 'permission:list-airports'], function () {
            Route::get('/', 'AirportController@index');
            Route::get('/fetch', 'AirportController@getAllAirports');
            Route::get('/mapview/{id}', 'AirportController@airportMapView');
            Route::get('/create', 'AirportController@create');
            Route::get('/edit/{id}', 'AirportController@airportEdit');
            Route::post('update/{airport}', 'AirportController@update');
            Route::post('store', 'AirportController@store');
            Route::get('/{id}', 'AirportController@getById');
            Route::get('/delete/{airport}', 'AirportController@delete');
            Route::get('/toggle_status/{airport}', 'AirportController@toggleAirportStatus');
        });

        Route::group(['prefix' => 'users',  'middleware' => 'permission:user-menu'], function () {
            // prefix('users')->group(function () {
            Route::get('/', 'UserController@index');
            Route::get('/fetch', 'UserController@getAllUser');
            Route::get('/create', 'UserController@create');
            Route::post('store', 'UserController@store');
            Route::get('edit/{user}', 'UserController@getById');
            Route::post('update/{user}', 'UserController@update');
            Route::get('toggle_status/{user}', 'UserController@toggleStatus');
            Route::get('delete/{user}', 'UserController@delete');
            Route::get('/request-list/{user}', 'UserController@UserTripRequest');
            Route::get('payment-history/{user}', 'UserController@userPaymentHistory');
            Route::post('payment-history/{user}', 'UserController@StoreUserPaymentHistory');
        });

        Route::group(['prefix' => 'sos',  'middleware' => 'permission:view-sos'], function () {
            // prefix('sos')->group(function () {
            Route::get('/', 'SosController@index');
            Route::get('/fetch', 'SosController@getAllSos');
            Route::get('/create', 'SosController@create');
            Route::post('store', 'SosController@store');
            Route::get('/{sos}', 'SosController@getById');
            Route::post('update/{sos}', 'SosController@update');
            Route::get('toggle_status/{sos}', 'SosController@toggleStatus');
            Route::get('delete/{sos}', 'SosController@delete');
        });

        Route::group(['prefix' => 'service_location',  'middleware' => 'permission:service_location'], function () {
            // prefix('service_location')->group(function () {
            Route::get('/', 'ServiceLocationController@index');
            Route::get('/fetch', 'ServiceLocationController@getAllLocation');
            Route::get('/create', 'ServiceLocationController@create');
            Route::post('store', 'ServiceLocationController@store');
            Route::get('edit/{service_location}', 'ServiceLocationController@getById');
            Route::post('update/{service_location}', 'ServiceLocationController@update');
            Route::get('toggle_status/{service_location}', 'ServiceLocationController@toggleStatus');
            Route::get('delete/{service_location}', 'ServiceLocationController@delete');
            Route::get('get/currency/', 'ServiceLocationController@getCurrencyByCountry')->name('getCurrencyByCountry');
        });

        Route::group(['prefix' => 'requests',  'middleware' => 'permission:view-requests'], function () {
            Route::get('/', 'RequestController@index');
            Route::get('/fetch', 'RequestController@getAllRequest');
            Route::get('/{request}', 'RequestController@getSingleRequest');
            Route::get('trip_view/{request}','RequestController@requestDetailedView');
            Route::get('/request/{request}', 'RequestController@fetchSingleRequest');
            Route::get('/fetch/request/{request}', 'RequestController@retrieveSingleRequest');
            Route::get('view-customer-invoice/{request_detail}','RequestController@viewCustomerInvoice');
            Route::get('view-driver-invoice/{request_detail}','RequestController@viewDriverInvoice');
            Route::get('cancelled/{request}', 'RequestController@getCancelledRequest');
            // Route::get('/delivery-rides', 'RequestController@indexDelivery');


        });

        Route::group(['prefix' => 'delivery-requests',  'middleware' => 'permission:view-delivery-requests'], function () {
            Route::get('/', 'RequestController@indexDelivery');
            Route::get('/fetch', 'RequestController@getAllDeliveryRequest');
            Route::get('cancelled/{request}', 'RequestController@getCancelledDeliveryRequest');


        });

         Route::group(['prefix' => 'scheduled-rides',  'middleware' => 'permission:view-requests'], function () {
            Route::get('/', 'RequestController@indexScheduled');
            Route::get('/fetch', 'RequestController@getAllScheduledRequest');

        });

        // Delivery Scheduled Rides CRUD
        Route::group(['prefix' => 'scheduled-delivery-rides',  'middleware' => 'permission:view-delivery-requests'], function () {
            Route::get('/', 'RequestController@indexScheduledDelivery');
            Route::get('/fetch', 'RequestController@getAllScheduledDeliveryRequest');

        });

         // Cancellation Rides Reason CRUD
        Route::group(['prefix' => 'cancellation-rides',  'middleware' => 'permission:view-requests'], function () {
            Route::get('/', 'CancellationRideController@index');
            Route::get('/fetch', 'CancellationRideController@getAllRides');

        });

        // Delivery Cancellation Rides CRUD
        Route::group(['prefix' => 'cancellation-delivery-rides',  'middleware' => 'permission:view-delivery-requests'], function () {
            Route::get('/', 'CancellationRideController@indexDelivery');
            Route::get('/fetch', 'CancellationRideController@getAllDeliveryRides');

        });

        // Cancellation Reason CRUD
        Route::group(['prefix' => 'cancellation',  'middleware' => 'permission:cancellation-reason'], function () {
            Route::get('/', 'CancellationReasonController@index');
            Route::get('/fetch', 'CancellationReasonController@fetch');
            Route::get('/create', 'CancellationReasonController@create');
            Route::post('store', 'CancellationReasonController@store');
            Route::get('/{reason}', 'CancellationReasonController@getById');
            Route::post('update/{reason}', 'CancellationReasonController@update');
            Route::get('toggle_status/{reason}', 'CancellationReasonController@toggleStatus');
            Route::get('delete/{reason}', 'CancellationReasonController@delete');
        });

         // Subscription Reason CRUD
         Route::group(['prefix' => 'subscription',  'middleware' => 'permission:manage-subscription'], function () {
            Route::get('/', 'SubscriptionController@index');
            Route::get('/fetch', 'SubscriptionController@fetch');
            Route::get('/create', 'SubscriptionController@create');
            Route::post('store', 'SubscriptionController@store');
            Route::get('/{sub}', 'SubscriptionController@getById');
            Route::post('update/{sub}', 'SubscriptionController@update');
            Route::get('delete/{sub}', 'SubscriptionController@delete');
        });

         // Order Reason CRUD
         Route::group(['prefix' => 'order',  'middleware' => 'permission:manage-order'], function () {
            Route::get('/', 'OrderController@index');
            Route::get('/fetch', 'OrderController@fetch');
            Route::get('/create', 'OrderController@create');
            Route::get('/upgrade/{order}', 'OrderController@upgrade');
            Route::post('package-upgrade', 'OrderController@packageUpgrade');
            Route::get('/package-show', 'OrderController@packageShow');
            Route::get('/invoice', 'OrderController@invoice')->name('invoice');
            Route::get('/invoice/{id}', 'OrderController@orderInvoice')->name('order.invoice');
            Route::post('store', 'OrderController@store');
            Route::get('/{order}', 'OrderController@getById');
            Route::post('update/{order}', 'OrderController@update');
            Route::get('delete/{order}', 'OrderController@delete');

            Route::post('/paypal/payment', [PayPalController::class, 'createPayment'])->name('paypal.payment');
            Route::get('/paypal/success', [PayPalController::class, 'paymentSuccess'])->name('payment.success');
            Route::get('/paypal/cancel', [PayPalController::class, 'paymentCancel'])->name('payment.cancel');
        });



        // Order Reason CRUD
        Route::group(['prefix' => 'blogs',  'middleware' => 'permission:manage-blogs'], function () {
            Route::get('/', 'BlogController@index');
            Route::get('/fetch', 'BlogController@fetch');
            Route::get('/create', 'BlogController@create');
            Route::post('store', 'BlogController@store');
            Route::get('/{blog}', 'BlogController@getById');
            Route::post('update/{blog}', 'BlogController@update');
            Route::get('delete/{blog}', 'BlogController@delete');
        });

        // Order Reason CRUD
        Route::group(['prefix' => 'blog-category',  'middleware' => 'permission:blog-category'], function () {
            Route::get('/', 'BlogCategoryController@index');
            Route::get('/fetch', 'BlogCategoryController@fetch');
            Route::get('/create', 'BlogCategoryController@create');
            Route::post('store', 'BlogCategoryController@store');
            Route::get('/{blogCategory}', 'BlogCategoryController@getById');
            Route::post('update/{blogCategory}', 'BlogCategoryController@update');
            Route::get('delete/{blogCategory}', 'BlogCategoryController@delete');
        });

        // Contact enquery fetch
        Route::group(['prefix' => 'contact',  'middleware' => 'permission:manage-contact'], function () {
            Route::get('/', 'ContactController@index');
            Route::get('/fetch', 'ContactController@fetch');
            Route::get('/show/{id}', 'ContactController@show');
            Route::post('is_view', 'ContactController@is_view');
        });

        // Our Team CRUD
        Route::group(['prefix' => 'our-team',  'middleware' => 'permission:manage-our-team'], function () {
            Route::get('/', 'OurTeamController@index');
            Route::get('/fetch', 'OurTeamController@fetch');
            Route::get('/create', 'OurTeamController@create');
            Route::post('store', 'OurTeamController@store');
            Route::get('/{team}', 'OurTeamController@getById');
            Route::post('update/{team}', 'OurTeamController@update');
            Route::get('delete/{team}', 'OurTeamController@delete');
        });

        // Faq CRUD
        Route::group(['prefix' => 'faq',  'middleware' => 'permission:manage-faq'], function () {
            Route::get('/', 'FaqController@index');
            Route::get('/fetch', 'FaqController@fetch');
            Route::get('/create', 'FaqController@create');
            Route::post('store', 'FaqController@store');
            Route::get('/{faq}', 'FaqController@getById');
            Route::post('update/{faq}', 'FaqController@update');
            Route::get('toggle_status/{faq}', 'FaqController@toggleStatus');
            Route::get('delete/{faq}', 'FaqController@delete');
        });

        // Gallery CRUD
        Route::group(['prefix' => 'galleries',  'middleware' => 'permission:manage-gallery'], function () {
            Route::get('/', 'GalleryController@index');
            Route::get('/fetch', 'GalleryController@fetch');
            Route::get('/create', 'GalleryController@create');
            Route::post('store', 'GalleryController@store');
            Route::get('/{gallery}', 'GalleryController@getById');
            Route::post('update/{gallery}', 'GalleryController@update');
            Route::get('toggle_status/{gallery}', 'GalleryController@toggleStatus');
            Route::get('delete/{gallery}', 'GalleryController@delete');
        });

        // Gallery CRUD
        Route::group(['prefix' => 'our-partner',  'middleware' => 'permission:manage-partner'], function () {
            Route::get('/', 'OurPartnerController@index');
            Route::get('/fetch', 'OurPartnerController@fetch');
            Route::get('/create', 'OurPartnerController@create');
            Route::post('store', 'OurPartnerController@store');
            Route::get('/{partner}', 'OurPartnerController@getById');
            Route::post('update/{partner}', 'OurPartnerController@update');
            Route::get('toggle_status/{partner}', 'OurPartnerController@toggleStatus');
            Route::get('delete/{partner}', 'OurPartnerController@delete');
        });

         // Order Reason CRUD
        Route::group(['prefix' => 'chat',  'middleware' => 'permission:manage-chat'], function () {
            Route::get('/', 'ChatController@index');
            Route::get('/fetch', 'ChatController@fetch');
            Route::get('/getConversations', 'ChatController@getConversations');
            Route::post('send', 'ChatController@store');
            Route::get('/{id}', 'ChatController@getById')->name('chatGetById');
            Route::post('seen','ChatController@updateSeen');
            Route::get('delete/{chat}', 'ChatController@delete');
        });


        // Promo Codes CRUD
        Route::group(['prefix' => 'promo',  'middleware' => 'permission:manage-promo'], function () {
            Route::get('/', 'PromoCodeController@index');
            Route::get('/fetch', 'PromoCodeController@fetch');
            Route::get('/create', 'PromoCodeController@create');
            Route::post('store', 'PromoCodeController@store');
            Route::get('/{promo}', 'PromoCodeController@getById');
            Route::post('update/{promo}', 'PromoCodeController@update');
            Route::get('toggle_status/{promo}', 'PromoCodeController@toggleStatus');
            Route::get('delete/{promo}', 'PromoCodeController@delete');
        });

        // Manage Notifications
        Route::group(['prefix' => 'notifications',  'middleware' => 'permission:notifications'], function () {
            Route::get('/push', 'NotificationController@index');
            Route::get('push/fetch', 'NotificationController@fetch');
            Route::get('push/view', 'NotificationController@pushView');
            Route::post('push/send', 'NotificationController@sendPush');
            Route::get('push/delete/{notification}', 'NotificationController@delete');
        });

        // Complaint Title CRUD
        Route::group(['prefix' => 'complaint/title',  'middleware' => 'permission:complaints'], function () {
            Route::get('/', 'ComplaintTitleController@index');
            Route::get('/fetch', 'ComplaintTitleController@fetch');
            Route::get('/create', 'ComplaintTitleController@create');
            Route::post('store', 'ComplaintTitleController@store');
            Route::get('/{title}', 'ComplaintTitleController@getById');
            Route::post('update/{title}', 'ComplaintTitleController@update');
            Route::get('toggle_status/{title}', 'ComplaintTitleController@toggleStatus');
            Route::get('delete/{title}', 'ComplaintTitleController@delete');
        });

        Route::group(['prefix' => 'complaint'], function () {
            Route::get('/users', 'ComplaintController@userComplaint');
            Route::get('/users/general', 'ComplaintController@userGeneralComplaint');
            Route::get('/users/request', 'ComplaintController@userRequestComplaint');
            Route::get('/drivers', 'ComplaintController@driverComplaint');
             Route::get('/drivers/general', 'ComplaintController@driverGeneralComplaint');
            Route::get('/drivers/request', 'ComplaintController@driverRequestComplaint');
            Route::get('/owner', 'ComplaintController@ownerComplaint');
             Route::get('/owner/general', 'ComplaintController@ownerGeneralComplaint');
            Route::get('/owner/request', 'ComplaintController@ownerRequestComplaint');
            Route::get('/taken/{complaint}', 'ComplaintController@takeComplaint');
            Route::get('/solved/{complaint}', 'ComplaintController@solveComplaint');
        });

        // Report page
        Route::group(['prefix' => 'reports',  'middleware' => 'permission:reports'], function () {
            Route::get('/user', 'ReportController@userReport')->name('userReport');
            Route::get('/driver', 'ReportController@driverReport')->name('driverReport');

            Route::get('/owner', 'ReportController@ownerReport')->name('ownerReport');


            Route::get('/driver-duties', 'ReportController@driverDutiesReport')->name('driverDutiesReport');
            Route::get('/travel', 'ReportController@travelReport')->name('travelReport');
            Route::any('/download', 'ReportController@downloadReport')->name('downloadReport');
        });

        // Manage Map
        Route::group(['prefix' => 'map',  'middleware' => 'permission:manage-map'], function () {
            Route::get('/view', 'MapController@mapView')->name('mapView');
            Route::get('/mapbox-view', 'MapController@mapViewMapbox')->name('mapViewMapbox');
            Route::get('/heatmap{zone_id?}', 'MapController@heatMapView')->name('heatMapView');
            Route::get('/get/zone', 'MapController@getZoneByServiceLocation')->name('getZoneByServiceLocation');
        });
    //Vehicle Type Fair
        Route::group(['prefix' => 'vehicle_fare'], function () {
            Route::get('/', 'VehicleFareController@index');
            Route::get('/fetch', 'VehicleFareController@fetchFareList');
            Route::get('/create', 'VehicleFareController@create');
            Route::get('fetch/vehicles', 'VehicleFareController@fetchVehiclesByZone');
            Route::post('store', 'VehicleFareController@store');
            Route::get('edit/{zone_price}', 'VehicleFareController@getById');
            Route::post('update/{zone_price}', 'VehicleFareController@update');
            Route::get('toggle_status/{zone_price}', 'VehicleFareController@toggleStatus');
            Route::get('delete/{zone_price}', 'VehicleFareController@delete');
            Route::get('/set/default/{zone_price}', 'ZoneController@setDefaultType');

            Route::get('/rental_package/index/{zone_type}', 'ZoneController@packageIndex');

            Route::get('/rental_package/create/{zone_type}', 'ZoneController@packageCreate');
            Route::post('/rental_package/store/{zone_type}', 'ZoneController@packageStore');
            Route::get('rental_package/edit/{package}', 'ZoneController@packageEdit');
            Route::post('/rental_package/update/{package}', 'ZoneController@packageUpdate');
            Route::get('/rental_package/delete/{package}', 'ZoneController@packageDelete');
            Route::get('/rental_package/toggleStatus/{package}', 'ZoneController@PackagetoggleStatus');
            Route::get('get/type', 'VehicleFareController@getTransportTypes')->name('getTransportTypes');
            Route::get('fetch/trip_type', 'VehicleFareController@fetchTriptype');


        });
    });

    Route::namespace('Master')->group(function () {

        Route::prefix('roles')->group(function () {
            Route::get('/', 'RoleController@index');
            Route::get('create', 'RoleController@create');
            Route::post('store', 'RoleController@store');
            Route::get('edit/{id}', 'RoleController@getById');
            Route::post('update/{role}', 'RoleController@update');
            Route::get('assign/permissions/{id}', 'RoleController@assignPermissionView');
            Route::post('assign/permissions/update/{role}', 'RoleController@attachAndDetachPermissions');
        });
        Route::prefix('system/settings')->group(function () {
            Route::get('/', 'SettingController@index');
            Route::post('/', 'SettingController@store');
        });

        // Car Make CRUD
        Route::group(['prefix' => 'carmake',  'middleware' => 'permission:manage-carmake'], function () {
            Route::get('/', 'CarMakeController@index');
            Route::get('/fetch', 'CarMakeController@fetch');
            Route::get('/create', 'CarMakeController@create');
            Route::post('store', 'CarMakeController@store');
            Route::get('/{make}', 'CarMakeController@getById');
            Route::post('update/{make}', 'CarMakeController@update');
            Route::get('toggle_status/{make}', 'CarMakeController@toggleStatus');
            Route::get('delete/{make}', 'CarMakeController@delete');
        Route::get('get/vehicle_make', 'CarMakeController@getVehicleMake')->name('getVehicleMake');

        });

        // Car Model CRUD
        Route::group(['prefix' => 'carmodel',  'middleware' => 'permission:manage-carmodel'], function () {
            Route::get('/', 'CarModelController@index');
            Route::get('/fetch', 'CarModelController@fetch');
            Route::get('/create', 'CarModelController@create');
            Route::post('store', 'CarModelController@store');
            Route::get('/{model}', 'CarModelController@getById');
            Route::post('update/{model}', 'CarModelController@update');
            Route::get('toggle_status/{model}', 'CarModelController@toggleStatus');
            Route::get('delete/{model}', 'CarModelController@delete');
        });

        Route::prefix('mail_templates')->group(function () {

            Route::get('/', 'MailTemplateController@index');
            Route::get('/fetch', 'MailTemplateController@fetch');
            Route::get('/create', 'MailTemplateController@create');
            Route::post('store', 'MailTemplateController@store');
            Route::get('/{mailTemplate}', 'MailTemplateController@getById');
            Route::post('update/{mailTemplate}', 'MailTemplateController@update');
            Route::get('toggle_status/{mailTemplate}', 'MailTemplateController@toggleStatus');
            Route::get('delete/{mailTemplate}', 'MailTemplateController@delete');

        });
        // Driver Needed Document CRUD
        Route::group(['prefix' => 'needed_doc',  'middleware' => 'permission:manage-driver-needed-document'], function () {
            Route::get('/', 'DriverNeededDocumentController@index');
            Route::get('/fetch', 'DriverNeededDocumentController@fetch');
            Route::get('/create', 'DriverNeededDocumentController@create');
            Route::post('store', 'DriverNeededDocumentController@store');
            Route::get('/{needed_doc}', 'DriverNeededDocumentController@getById');
            Route::post('update/{needed_doc}', 'DriverNeededDocumentController@update');
            Route::get('toggle_status/{needed_doc}', 'DriverNeededDocumentController@toggleStatus');
            Route::get('delete/{needed_doc}', 'DriverNeededDocumentController@delete');
        });
         // Owner Needed Document CRUD
                Route::group(['prefix' => 'owner_needed_doc',  'middleware' => 'permission:manage-owner-needed-document'], function () {
                    Route::get('/', 'OwnerNeededDocumentController@index');
                    Route::get('/fetch', 'OwnerNeededDocumentController@fetch');
                    Route::get('/create', 'OwnerNeededDocumentController@create');
                    Route::post('store', 'OwnerNeededDocumentController@store');
                    Route::get('/{needed_doc}', 'OwnerNeededDocumentController@getById');
                    Route::post('update/{needed_doc}', 'OwnerNeededDocumentController@update');
                    Route::get('toggle_status/{needed_doc}', 'OwnerNeededDocumentController@toggleStatus');
                    Route::get('delete/{needed_doc}', 'OwnerNeededDocumentController@delete');
                });
          // Fleet Needed Document CRUD
            Route::group(['prefix' => 'fleet_needed_doc',  'middleware' => 'permission:manage-fleet-needed-document'], function () {
                Route::get('/', 'FleetNeededDocumentController@index');
                Route::get('/fetch', 'FleetNeededDocumentController@fetch');
                Route::get('/create', 'FleetNeededDocumentController@create');
                Route::post('store', 'FleetNeededDocumentController@store');
                Route::get('/{needed_doc}', 'FleetNeededDocumentController@getById');
                Route::post('update/{needed_doc}', 'FleetNeededDocumentController@update');
                Route::get('toggle_status/{needed_doc}', 'FleetNeededDocumentController@toggleStatus');
                Route::get('delete/{needed_doc}', 'FleetNeededDocumentController@delete');
                });
        // Package type CRUD
        Route::group(['prefix' => 'package_type',  'middleware' => 'permission:package-type'], function () {
            Route::get('/', 'PackageTypeController@index');
            Route::get('/fetch', 'PackageTypeController@fetch');
            Route::get('/create', 'PackageTypeController@create');
            Route::post('store', 'PackageTypeController@store');
            Route::get('/{package}', 'PackageTypeController@getById');
            Route::post('update/{package}', 'PackageTypeController@update');
            Route::get('toggle_status/{package}', 'PackageTypeController@toggleStatus');
            Route::get('delete/{package}', 'PackageTypeController@delete');
        });

        // Goods Type CRUD
        Route::group(['prefix' => 'goods-types',  'middleware' => 'permission:manage-goods-types'], function () {
            Route::get('/', 'GoodsTypesController@index');
            Route::get('/fetch', 'GoodsTypesController@fetch');
            Route::get('/create', 'GoodsTypesController@create');
            Route::post('store', 'GoodsTypesController@store');
            Route::get('/{goods_type}', 'GoodsTypesController@getById');
            Route::post('update/{goods_type}', 'GoodsTypesController@update');
            Route::get('toggle_status/{goods_type}', 'GoodsTypesController@toggleStatus');
            Route::get('delete/{goods_type}', 'GoodsTypesController@delete');
        });
        // Banner image CRUD
        Route::group(['prefix' => 'banner_image',  'middleware' => 'permission:banner-image'], function () {
            Route::get('/', 'BannerImageController@index');
            Route::get('/fetch', 'BannerImageController@fetch');
            Route::get('/create', 'BannerImageController@create');
            Route::post('store', 'BannerImageController@store');
            Route::get('/edit/{bannerImage}', 'BannerImageController@getById');
            Route::post('update/{bannerImage}', 'BannerImageController@update');
            Route::get('toggle_status/{bannerImage}', 'BannerImageController@toggleStatus');
            Route::get('delete/{bannerImage}', 'BannerImageController@delete');
        });
         // OTP  CRUD
        Route::group(['prefix' => 'otp',  'middleware' => 'permission:otp'], function () {
            Route::get('/', 'OtpController@index');
            Route::get('/fetch', 'OtpController@fetch');
        });

    });
});

    Route::middleware('auth:web')->namespace('Dispatcher')->group(function () {
        Route::prefix('dispatch')->group(function () {
        Route::get('/new', 'DispatcherController@dispatchView');
        Route::get('/', 'DispatcherController@index');
        Route::post('create/request', 'DispatcherController@createRequest');
        Route::get('/request/{requestmodel}', 'DispatcherController@fetchSingleRequest');

    });
});

