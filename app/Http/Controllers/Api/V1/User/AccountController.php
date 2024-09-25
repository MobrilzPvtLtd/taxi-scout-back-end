<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\User;
use App\Models\Admin\Driver;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\ApiController;
use App\Models\Admin\Order;
use App\Models\Admin\Owner;
use App\Transformers\User\UserTransformer;
use App\Transformers\Driver\DriverProfileTransformer;
use App\Transformers\Owner\OwnerProfileTransformer;

class AccountController extends ApiController
{
    /**
     * Get the current logged in user.
     * @group User-Management
     * @return \Illuminate\Http\JsonResponse
    * @responseFile responses/auth/authenticated_driver.json
    * @responseFile responses/auth/authenticated_user.json
     */
    public function me()
    {
        $user = auth()->user();
        if (auth()->user()->hasRole(Role::DRIVER)) {
            $owner = Owner::whereHas('user', function ($query) use ($user) {
                $query->where('owner_unique_id', $user->driver->owner_id);
            })->first();

            if (!$owner) {
                $this->throwCustomException('The taxi company is not found.');
            }

            $packageExpiryDate = Order::where('user_id', $owner->user_id)->where('active', 2)->first();

            if ($packageExpiryDate) {
                $this->throwCustomException('Your company’s subscription has expired. Please renew your company package.');
            }

            $driver_details = $user->driver;
            $user = fractal($driver_details, new DriverProfileTransformer)->parseIncludes(['onTripRequest.userDetail','onTripRequest.requestBill','metaRequest.userDetail','driverVehicleType']);
            // dd($user);

        } else if(auth()->user()->hasRole(Role::USER)) {
            $user = fractal($user, new UserTransformer)->parseIncludes(['onTripRequest.driverDetail','onTripRequest.requestBill','metaRequest.driverDetail','favouriteLocations','laterMetaRequest.driverDetail']);

        }else{
            $owner_details = $user->owner;
            $user = fractal($owner_details, new OwnerProfileTransformer);
        }

        // if(auth()->user()->hasRole(Role::DISPATCHER)){
        //     $user = User::where('id',auth()->user()->id)->first();
        // }
        return $user;
        // return $this->respondOk($user);
    }
}
