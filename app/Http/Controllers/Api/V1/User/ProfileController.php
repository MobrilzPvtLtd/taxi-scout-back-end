<?php

namespace App\Http\Controllers\Api\V1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\User\UserTransformer;
use App\Transformers\Driver\DriverTransformer;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\UpdateDriverProfileRequest;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Base\Constants\Auth\Role;
use App\Models\Admin\Order;
use App\Models\Admin\Owner;
use App\Models\User;
use App\Models\Request\FavouriteLocation;
use App\Models\Payment\UserBankInfo;
use Kreait\Firebase\Contract\Database;
use App\Transformers\Driver\DriverProfileTransformer;
use App\Transformers\Owner\OwnerProfileTransformer;

/**
 * @group Profile-Management
 *
 * APIs for Profile-Management
 */
class ProfileController extends ApiController
{
    /**
     * ImageUploader instance.
     *
     * @var ImageUploaderContract
     */
    protected $imageUploader;

    protected $user;

    protected $database;

    /**
     * ProfileController constructor.
     *
     * @param ImageUploaderContract $imageUploader
     */
    public function __construct(ImageUploaderContract $imageUploader,User $user,Database $database)
    {
        $this->imageUploader = $imageUploader;

        $this->user = $user;

        $this->database = $database;

    }

    /**
     * Update user profile.
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        if($user->driver){
            $owner = Owner::whereHas('user', function ($query) use ($user) {
                $query->where('owner_unique_id', $user->driver->owner_id);
            })->first();
        }else{
            $owner = Owner::whereHas('user', function ($query) use ($user) {
                $query->where('owner_unique_id', $user->owner_id);
            })->first();
        }

        if (!$owner) {
            $this->throwCustomException('The taxi company is not found.');
        }

        $packageExpiryDate = Order::where('user_id', $owner->user_id)->where('active', 2)->first();

        if ($packageExpiryDate) {
            $this->throwCustomException('Your company’s subscription has expired. Please renew your company package.');
        }

        $data = $request->only(['name', 'email', 'last_name','mobile','service_location_id']);

        if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
            $data['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }

        $mobile = $request->mobile;
        $data['vehicle_type'] = $request->vehicle_type_id;

        if($mobile){
             $validate_exists_mobile = $this->user->belongsTorole(Role::USER)->where('mobile', $mobile)->where('id','!=',$user->id)->exists();

            if ($validate_exists_mobile) {
                $this->throwCustomException('Provided mobile has already been taken');
            }

        }

        $user->update($data);

        if (auth()->user()->hasRole(Role::DRIVER)) {

            if($request->has('vehicle_types')){
                foreach (json_decode($request->vehicle_types) as $key => $type) {
                    $user->driver->driverVehicleTypeDetail()->create(['vehicle_type'=>$type]);
                }
            }

            // // Store records to firebase
            $this->database->getReference('drivers/'.'driver_'.$user->driver->id)->set(['id'=>$user->driver->id,'vehicle_type'=>$request->input('vehicle_type'),'active'=>1,'gender'=>$user->driver->gender,'updated_at'=> Database::SERVER_TIMESTAMP]);

            $user->driver->update($data);
        }
        $user = fractal($user->fresh(), new UserTransformer);

        return $this->respondSuccess($user);
    }

    /**
    * Update Driver Profile
    *
    */
    public function updateDriverProfile(UpdateDriverProfileRequest $request)
    {

        $user_params = $request->only(['name', 'email', 'last_name','mobile']);


        $user = auth()->user();


        $mobile = $request->mobile;

        if($mobile){
             $validate_exists_mobile = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $mobile)->where('id','!=',$user->id)->exists();

        if ($validate_exists_mobile) {
            $this->throwCustomException('Provided mobile has already been taken');
        }

        }

        $driver_params = $request->only(['vehicle_type','car_make','car_model','car_color','car_number','name','email','vehicle_year']);


         $driver_params['approve'] = true;

         $driver_params['reason'] = 'profile-info-updated';

		$driver_params['smoking'] = $request->smoking;
		$driver_params['pets'] = $request->pets;
		$driver_params['drinking'] = $request->drinking;
		$driver_params['handicaped'] = $request->handicaped;


//dd($driver_params);
        if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
            $user_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }

        if ($uploadedFile = $this->getValidatedUpload('profile_pic', $request)) {
            $user_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();

            $driver_params['approve'] = false;

            $driver_params['reason'] = 'profile-info-updated';

        }

        $user->update($user_params);



        if($request->has('vehicle_type') && $request->vehicle_type){

            $driver_params['approve'] = false;

            $driver_params['reason'] = 'vehicle-info-updated';


        }

        if($driver_params['approve']==false){

            $status=0;

            $this->database->getReference('drivers/driver_'.$user->driver->id)->update(['approve'=>(int)$status,'updated_at'=> Database::SERVER_TIMESTAMP]);

        }


        if(env('APP_FOR')=='demo'){

            $driver_params['approve']=true;
            $status = true;

            $this->database->getReference('drivers/driver_'.$user->driver->id)->update(['approve'=>(int)$status,'updated_at'=> Database::SERVER_TIMESTAMP]);

        }


        $user->driver()->update($driver_params);

        $driver_details = $user->driver;


        if($request->has('vehicle_types')){

            $driver_details->driverVehicleTypeDetail()->delete();

            foreach (json_decode($request->vehicle_types) as $key => $type) {

                $driver_details->driverVehicleTypeDetail()->create(['vehicle_type'=>$type]);

            }

        }

        $result = fractal($driver_details, new DriverTransformer)->parseIncludes(['onTripRequest.userDetail','onTripRequest.requestBill','metaRequest.userDetail']);

        return $this->respondOk($result);
    }

    /**
     * Update user password.
     * @bodyParam old_password password required old_password provided user
     * @bodyParam password password required password provided user
     * @bodyParam password_confirmation password required  confirmed password provided user
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
     */
    public function updatePassword(ChangePasswordRequest $request)
    {
        $oldPassword = $request->input('old_password');
        $password = $request->input('password');

        $user = auth()->user();

        if (!hash_check($oldPassword, $user->password)) {
            $this->throwCustomValidationException('Invalid old password entered.', 'old_password');
        }

        $user->forceFill(['password' => bcrypt($password)])->save();

        return $this->respondSuccess();
    }

    /**
    * Update My Language
    * @bodyParam lang string required language provided user
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
    */
    public function updateMyLanguage(Request $request)
    {
        // Validate Request id
        $request->validate([
        'lang' => 'required',
        ]);
        $user = auth()->user();
        $user->forceFill(['lang' => $request->lang])->save();
        return $this->respondSuccess();
    }

    /**
    * Add favourite location
    * @bodyParam pick_lat double required pikup lat of the user
    * @bodyParam pick_lng double required pikup lng of the user
    * @bodyParam drop_lat double optional drop lat of the user
    * @bodyParam drop_lng double optional drop lng of the user
    * @bodyParam pick_address string required pickup address of the favourite location
    * @bodyParam drop_address string optional drop address of the favourite location
    * @bodyParam address_name string required address name of the favourite location
    * @bodyParam landmark string optional drop address of the favourite location
    * @response
     * {
     *"success": true,
     *"message": "address added successfully"
     *}
    */
    public function addFavouriteLocation(Request $request){

        // Validate Request id
        $request->validate([
            'pick_lat'  => 'required',
            'pick_lng'  => 'required',
            'pick_address'=>'required',
            'drop_lat'  =>'sometimes|required',
            'drop_lng'  =>'sometimes|required',
            'drop_address'=>'sometimes|required',
            'address_name'=>'required',
        ]);

        $created_params = $request->all();

        $created_params['user_id'] = auth()->user()->id;

        $locations = FavouriteLocation::where('user_id',auth()->user()->id)->get()->count();

        if($locations==4){
            $this->throwCustomException('You have reached your limits');
        }
        FavouriteLocation::create($created_params);

        return $this->respondSuccess(null,'address added successfully');



    }

    /**
    * List Favourite Locations
    *
    */
    public function FavouriteLocationList()
    {
        $user = auth()->user();

        $locations = FavouriteLocation::where('user_id',$user->id)->get();

        return $this->respondSuccess($locations,'address listed successfully');

    }

    /**
     * Delete Favourite Location
     *
     * @response
     * {
     * "success": true,
     * "message": "favorite location deleted successfully"
     * }
     * */
    public function deleteFavouriteLocation(FavouriteLocation $favourite_location){

        $favourite_location->delete();

        return $this->respondSuccess(null,'favorite location deleted successfully');


    }

    /**
     * Add/Update Bank Info
     * @bodyParam account_name string required name of the account
     * @bodyParam account_no integer required Number of the account
     * @bodyParam bank_code string required Bank code of the account
     * @bodyParam bank_name string required Bank name of the account
     *
     *
     * */
    public function updateBankinfo(Request $request)
    {
        $user = auth()->user();

        $bankInfo = $user->bankInfo;

        if($bankInfo){

           $user->bankInfo()->update($request->all());

        }else{
            $bankInfo = $user->bankInfo()->create($request->all());

        }

        return $this->respondSuccess(null,'bank info updated successfully');

    }

    /**
     * Get Bank info
     *
     *
     * */
    public function getBankInfo()
    {
        $user = auth()->user();

        $bankInfo = $user->bankInfo;

        return response()->json(['success'=>true,'message'=>'bank info listed successfully','data'=>$bankInfo]);


    }
    /**
     * user Account Delete
     *
     *
     * */
    public function userDeleteAccount()
    {
        $user = auth()->user()->delete();

        return response()->json(['success'=>true,'message'=>'User Account deleted successfully']);

    }

}
