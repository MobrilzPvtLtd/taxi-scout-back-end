<?php

namespace App\Http\Controllers\Api\V1\Auth\Registration;

use App\Models\User;
use App\Models\Country;
use App\Models\Admin\Driver;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\DB;
use App\Events\Auth\UserRegistered;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\ServiceLocation;
use App\Base\Constants\Masters\WalletRemarks;
use App\Http\Controllers\Api\V1\BaseController;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Base\Services\OTP\Handler\OTPHandlerContract;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Requests\Auth\Registration\DriverRegistrationRequest;
use App\Jobs\Notifications\Auth\Registration\UserRegistrationNotification;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Master\MailTemplate;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\Mails\SendMailNotification;
use App\Mail\OtpMail;
use App\Models\Admin\AdminDetail;
use App\Models\Admin\DriverVehicleType;
use App\Models\Admin\Order;
use App\Models\Admin\Owner;
use App\Models\Admin\Subscription;
use App\Models\Admin\UserDetails;
use App\Models\MailOtp;

/**
 * @group SignUp-And-Otp-Validation
 *
 * APIs for Driver Register
 */
class DriverSignupController extends LoginController
{
    protected $user;
    protected $driver;
    protected $otpHandler;
    protected $country;
    protected $database;
    protected $imageUploader;



    public function __construct(User $user, Driver $driver, Country $country, OTPHandlerContract $otpHandler, Database $database,ImageUploaderContract $imageUploader)
    {
        $this->user = $user;
        $this->driver = $driver;
        $this->otpHandler = $otpHandler;
        $this->country = $country;
        $this->database = $database;
        $this->imageUploader = $imageUploader;
    }

    public function register(DriverRegistrationRequest $request)
    {
        $owner = Owner::whereHas('user', function ($query) use ($request) {
            $query->where('owner_unique_id', $request->input('owner_id'));
        })->first();

        $registeredDriverCount = Driver::where('owner_id', $request->input('owner_id'))->count();
        $packageExpiryDate = Order::where('user_id', $owner->user_id)->where('active', 2)->first();

        if ($packageExpiryDate) {
            $this->throwCustomException('Your company’s subscription has expired. Please renew your company package.');
        }

        $packageDate = Order::where('user_id', $owner->user_id)->first();

        if (!$packageDate) {
            $this->throwCustomException('No active order found for this user. Please check your registration.');
        }

        $package = Subscription::find($packageDate->package_id);

        if (!$package) {
            $this->throwCustomException('Package not found. Please check your subscription details.');
        }

        if ($registeredDriverCount >= $package->number_of_drivers) {
            $this->throwCustomException('Your company has reached the limit of registered drivers allowed under your company current package.');
        }

        $mobileUuid = $request->input('uuid');
        Log::info($request->all());
        $created_params = $request->only(['service_location_id', 'name','mobile','email','address','state','city','country','gender','vehicle_type','car_make','car_model','car_color','car_number','vehicle_year','custom_make','custom_model','smoking','pets','drinking','handica','driving_license']);

        $mobile = $request->mobile;

        $created_params['postal_code'] = $request->postal_code;

        if ($request->input('service_location_id')) {
            $timezone = ServiceLocation::where('id', $request->input('service_location_id'))->pluck('timezone')->first();
        } else {
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }

        $country_code = $this->country->where('dial_code', $request->input('country'))->exists();

        if (!$country_code) {
            $this->throwCustomException('unable to find country');
        }
        $country_id = $this->country->where('dial_code', $request->input('country'))->pluck('id')->first();

        $created_params['country'] = $country_id;

        $profile_picture = null;

        if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
            $profile_picture = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }

        $validate_exists_email = $this->user->belongsTorole([Role::DRIVER,Role::OWNER,Role::USER])->where('email', $request->email)->exists();

        if ($validate_exists_email) {
            $this->throwCustomException('Provided email has already been taken');
        }

        $owner_id = $request->owner_id;

        $validate_exists_mobile = $this->user->belongsTorole([Role::DRIVER,Role::ADMIN,Role::USER])->where('mobile', $mobile)->exists();

        if ($validate_exists_mobile) {
            $this->throwCustomException('Provided mobile has already been taken');
        }


        $created_params['approve'] = 2;

        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->file('profile_picture')->store('profile_picture', 'public');
            $data = $request->except('profile_picture');
            $data['profile_picture'] = $profile_picture;
        }

        if ($request->has('email_confirmed') == true)
        {
            $data['email_confirmed'] = true;
        }

        $data = [
            'name' => $request->input('name'),
            'gender' => $request->input('gender'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'mobile' => $mobile,
            'active' => 0,
            'mobile_confirmed' => true,
            'fcm_token'=>$request->input('device_token'),
            'login_by'=>$request->input('login_by'),
            'timezone'=>$timezone,
            'country'=>$country_id,
            'profile_picture'=>$profile_picture,
            'refferal_code'=>str_random(6),
            'lang'=>$request->input('lang'),
            'smoking'=>$request->input('smoking'),
            'pets'=>$request->input('pets'),
            'drinking'=>$request->input('drinking'),
            'handica'=>$request->input('handica'),
        ];

        if (env('APP_FOR')=='demo' && $request->has('company_key') && $request->input('company_key')) {
            $data['company_key'] = $request->input('company_key');
        }


        if($request->has('is_bid_app')){
            $data['is_bid_app']=1;
        }

        $user = $this->user->create($data);

        $created_params['owner_id'] = $owner_id;
        $created_params['mobile'] = $mobile;
        $created_params['uuid'] = driver_uuid();
        $created_params['active'] = false;
        // if($request->has('transport_type')) {
        //     $created_params['transport_type'] = $request->transport_type;
        // }
        $created_params['transport_type'] = "taxi";

        $driver = $user->driver()->create($created_params);

        // if($request->has('vehicle_types')){
        //     $vehicleTypes = json_decode($request->vehicle_types);
        //     if ($vehicleTypes !== null && (is_array($vehicleTypes) || is_object($vehicleTypes))) {
        //         foreach ($vehicleTypes as $key => $type) {
        //             $driver->driverVehicleTypeDetail()->create(['vehicle_type'=>$type]);
        //         }
        //     }
        // }

        if($request->has('vehicle_types')){
            foreach (json_decode($request->vehicle_types) as $key => $type) {
                $driver->driverVehicleTypeDetail()->create(['vehicle_type'=>$type]);
            }
        }

        // // Store records to firebase
        $this->database->getReference('drivers/'.'driver_'.$driver->id)->set(['id'=>$driver->id,'vehicle_type'=>$request->input('vehicle_type'),'active'=>1,'gender'=>$driver->gender,'updated_at'=> Database::SERVER_TIMESTAMP]);

        $driver_detail_data = $request->only(['is_company_driver','company']);
        $driver_detail = $driver->driverDetail()->create($driver_detail_data);

        // Create Empty Wallet to the driver
        $driver_wallet = $driver->driverWallet()->create(['amount_added'=>0]);

        $user->attachRole(Role::DRIVER);

        // if(($user->email_confirmed) == true){

        //     /*mail Template*/
        //     $user_name = $user->name;
        //     $mail_template = MailTemplate::where('mail_type', 'welcome_mail_driver')->first();

        //     $description = $mail_template->description;

        //     $description = str_replace('$user_name', $user_name, $description);

        //     $mail_template->description = $description;

        //     $mail_template = $mail_template->description;

        //     $user_mail = $user->email;

        //     //dispatch(new SendMailNotification($mail_template, $user_mail));

        // /*mail Template*/
        // }
        event(new UserRegistered($user));

        $otpDigit = mt_rand(100000, 999999);

        $mail_otp = MailOtp::create([
            'email' => $request->email,
            'otp' => $otpDigit,
        ]);

        $otp = [
            'name' => $user->name,
            'email' => $user->email,
            'otp' => $mail_otp->otp,
        ];

        if ($request->has('email')) {
            Mail::to($user->email)->send(new OtpMail($otp));
        }

		// return $this->respondOk("Your account register successfully.");
        return $this->respondOk("Your account register successfully. Please check your email for 6 digit OTP");

        // $this->throwCustomException('Your account is pending approval. Please wait for our team to review and approve your account.');

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error('Error while Registering a driver account. Input params : ' . json_encode($request->all()));
        //     return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        // }
        // DB::commit();
        // return $this->authenticateAndRespond($user, $request, $needsToken=true);
    }

    /**
    * Validate Mobile-For-Driver
    * @bodyParam mobile integer required mobile of driver
     * @response {
     * "success":true,
     * "message":"mobile_validated",
     * }
    *
    */
    public function validateDriverMobile(Request $request)
    {
        $mobile = $request->mobile;


        $validate_exists_mobile = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $mobile)->exists();

        if($request->has('role') && $request->role=='driver'){

            $validate_exists_mobile = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $mobile)->exists();

        }
        if($request->has('role') && $request->role=='owner'){

             $validate_exists_mobile = $this->user->belongsTorole(Role::OWNER)->where('mobile', $mobile)->exists();
        }


        if($request->has('email')){

        $validate_exists_email = $this->user->belongsTorole(Role::DRIVER)->where('email', $request->email)->exists();

        if($request->has('role')&& $request->role=='driver'){

            $validate_exists_email = $this->user->belongsTorole(Role::DRIVER)->where('email', $request->email)->exists();
        }
        if($request->has('role')&& $request->role=='owner'){

            $validate_exists_email = $this->user->belongsTorole(Role::OWNER)->where('email', $request->email)->exists();
        }

        if ($validate_exists_email) {
            $this->throwCustomException('Provided email has already been taken');
        }

        }

        if ($validate_exists_mobile) {
            $this->throwCustomException('Provided mobile has already been taken');
        }

        return $this->respondSuccess(null, 'mobile_validated');
    }

    /**
    * Validate Mobile-For-Driver-For-Login
    * @bodyParam mobile integer required mobile of driver
     * @response {
     * "success":true,
     * "message":"mobile_exists",
     * }
     */
   public function validateDriverMobileForLogin(Request $request)
    {
      if ($request->has('mobile'))
        {

        $mobile = $request->mobile;

        $validate_exists_mobile = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $mobile)->exists();

        if($request->has('role') && $request->role=='driver'){

        $validate_exists_mobile = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $mobile)->exists();

        }
        if($request->has('role') && $request->role=='owner'){

            $validate_exists_mobile = $this->user->belongsTorole(Role::OWNER)->where('mobile', $mobile)->exists();
        }

        if ($validate_exists_mobile) {
            return $this->respondSuccess(null, 'mobile_exists');
        }

                return response()->json(['success'=>false,'message'=>'mobile_does_not_exists','enabled_module'=>get_settings('enable_modules_for_applications')]);

        // return $this->respondFailed('mobile_does_not_exists');
       }
      if ($request->has('email'))
        {

        $email = $request->email;

        $validate_exists_email = $this->user->belongsTorole(Role::DRIVER)->where('email', $email)->exists();

        if($request->has('role') && $request->role=='driver'){

        $validate_exists_email = $this->user->belongsTorole(Role::DRIVER)->where('email', $email)->exists();

        }
        if($request->has('role') && $request->role=='owner'){

            $validate_exists_email = $this->user->belongsTorole(Role::OWNER)->where('email', $email)->exists();
        }

        if ($validate_exists_email) {
            return $this->respondSuccess(null, 'email_exists');
        }

        return response()->json(['success'=>false,'message'=>'email_does_not_exists','enabled_module'=>get_settings('enable_modules_for_applications')]);
        // return $this->respondFailed('email_does_not_exists');
      }


    }



    /**
    * Add Commission to the referred driver
    *
    */
    public function addCommissionToRefferedUser($reffered_user)
    {
        $driver_wallet = $reffered_user->driverWallet;
        $referral_commision = get_settings('referral_commision_for_driver')?:0;

        $driver_wallet->amount_added += $referral_commision;
        $driver_wallet->amount_balance += $referral_commision;
        $driver_wallet->save();

        // Add the history
        $reffered_user->driverWalletHistory()->create([
            'amount'=>$referral_commision,
            'transaction_id'=>str_random(6),
            'remarks'=>WalletRemarks::REFERRAL_COMMISION,
            'refferal_code'=>$reffered_user->refferal_code,
            'is_credit'=>true]);

        // Notify user
        $title = trans('push_notifications.referral_earnings_notify_title');
        $body = trans('push_notifications.referral_earnings_notify_body');

        dispatch(new SendPushNotification($reffered_user->user,$title,$body));

    }


    /**
     * Owner Register
     * @bodyParam name string required name of the owner
     * @bodyParam company_name string required name of the the company
     * @bodyParam address string required address of the the company
     * @bodyParam city string required city of the the company
     * @bodyParam tax_number string required tax_number of the the company
     * @bodyParam country string required country dial code of the the company
     * @bodyParam postal_code string required postal_code of the the company
     * @bodyParam mobile integer required mobile of owner
     * @bodyParam email email required email of the owner
     * @bodyParam device_token string required device_token of the owner
     * @bodyParam service_location_id uuid required service location of the owner. it can be listed from service location list apis
     * @bodyParam login_by tinyInt required from which device the owner registered
     * @return \Illuminate\Http\JsonResponse
     * @responseFile responses/auth/register.json
     *
     * */
    public function ownerRegister(Request $request){

        $request->validate([
            'company_name' => 'required|unique:owners,company_name,NULL,id,deleted_at,NULL',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile'=>'required|unique:users,mobile',
            'address'=>'required|min:10',
            'postal_code'=>'required|numeric',
            'city'=>'required',
            'service_location_id' => 'sometimes|required',
            'tax_number' => 'required',
            'device_token'=>'required',
            'login_by'=>'required|in:android,ios',
            'country' =>'required|exists:countries,dial_code',
            'transport_type'=>'required'
        ]);


         $created_params = $request->only(['service_location_id','company_name','owner_name','mobile','email','address','postal_code','city','tax_number','name']);

         $created_params['owner_name'] = $request->name;

         $created_params['transport_type'] = $request->transport_type;

         $created_params['approve'] = false;

        if ($request->input('service_location_id')) {
            $timezone = ServiceLocation::where('id', $request->input('service_location_id'))->pluck('timezone')->first();
        } else {
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }

        $country_id = $this->country->where('dial_code', $request->input('country'))->pluck('id')->first();

        $profile_picture = null;

        if ($uploadedFile = $this->getValidatedUpload('profile', $request)) {
            $profile_picture = $this->imageUploader->file($uploadedFile)
                ->saveDriverProfilePicture();
        }

        $validate_exists_email = $this->user->belongsTorole([Role::DRIVER,Role::OWNER])->where('email', $request->email)->exists();

        if ($validate_exists_email) {
            $this->throwCustomException('Provided email has already been taken');
        }

        // $mobile = $this->otpHandler->getMobileFromUuid($mobileUuid);
        $mobile = $request->mobile;

        $validate_exists_mobile = $this->user->belongsTorole([Role::DRIVER,Role::OWNER])->where('mobile', $mobile)->exists();

        if ($validate_exists_mobile) {
            $this->throwCustomException('Provided mobile has already been taken');
        }
        if ($request->has('email_confirmed') == true)
        {
            $data['email_confirmed'] = true;
        }
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile' => $mobile,
            'mobile_confirmed' => true,
            'fcm_token'=>$request->input('device_token'),
            'login_by'=>$request->input('login_by'),
            'timezone'=>$timezone,
            'country'=>$country_id,
            'profile_picture'=>$profile_picture,
            'refferal_code'=>str_random(6),
        ];

        DB::beginTransaction();
        try {

        $user = $this->user->create($data);

        $user->attachRole(Role::OWNER);


        $owner = $user->owner()->create($created_params);

        $owner_wallet = $owner->ownerWalletDetail()->create(['amount_added'=>0]);


        $this->database->getReference('owners/'.$owner->id)->set(['id'=>$owner->id,'active'=>1,'updated_at'=> Database::SERVER_TIMESTAMP]);


        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            Log::error('Error while Registering a owner account. Input params : ' . json_encode($request->all()));
            return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        }
        DB::commit();
        return $this->authenticateAndRespond($user, $request, $needsToken=true);

    }
}
