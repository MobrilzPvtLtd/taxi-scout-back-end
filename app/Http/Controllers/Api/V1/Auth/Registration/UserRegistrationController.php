<?php

namespace App\Http\Controllers\Api\V1\Auth\Registration;

use DB;
use Twilio;
use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Events\Auth\UserLogin;
use App\Base\Constants\Auth\Role;
use App\Events\Auth\UserRegistered;
use Illuminate\Support\Facades\Log;
use App\Base\Libraries\SMS\SMSContract;
use App\Http\Controllers\ApiController;
use Laravel\Socialite\Facades\Socialite;
use App\Helpers\Exception\ExceptionHelpers;
use App\Jobs\Notifications\OtpNotification;
use Psr\Http\Message\ServerRequestInterface;
use App\Base\Constants\Masters\WalletRemarks;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Base\Services\OTP\Handler\OTPHandlerContract;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Helpers\Exception\throwCustomValidationException;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use App\Http\Requests\Auth\Registration\UserRegistrationRequest;
use App\Http\Requests\Auth\Registration\SendRegistrationOTPRequest;
use App\Http\Requests\Auth\Registration\ValidateRegistrationOTPRequest;
use App\Jobs\Notifications\Auth\Registration\UserRegistrationNotification;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Jobs\Notifications\SendPushNotification;

use App\Models\Master\MailTemplate;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\Mails\SendMailNotification;
use App\Models\MailOtp;
use App\Mail\OtpMail;
use App\Mail\AdminRegister;
use App\Mail\SuperAdminNotification;
use App\Http\Requests\Auth\Registration\ValidateEmailOTPRequest;
use App\Http\Requests\Auth\Registration\SendRegistrationMailOTPRequest;
use App\Models\Admin\AdminDetail;
use App\Models\Admin\Driver;
use Carbon\Carbon;

/**
 * @group SignUp-And-Otp-Validation
 *
 * APIs for User-Management
 */
class UserRegistrationController extends LoginController
{
    use ExceptionHelpers;
    /**
     * The OTP handler instance.
     *
     * @var \App\Base\Services\OTP\Handler\OTPHandlerContract
     */
    protected $otpHandler;

    /**
     * The user model instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    protected $smsContract;

    protected $imageUploader;

    protected $country;

    /**
     * UserRegistrationController constructor.
     *
     * @param \App\Models\User $user
     * @param \App\Base\Services\OTP\Handler\OTPHandlerContract $otpHandler
     */
    public function __construct(User $user, OTPHandlerContract $otpHandler, Country $country, SMSContract $smsContract,ImageUploaderContract $imageUploader)
    {
        $this->user = $user;
        $this->otpHandler = $otpHandler;
        $this->country = $country;
        $this->smsContract = $smsContract;
        $this->imageUploader = $imageUploader;

    }
    public function sendMailOTP(SendRegistrationMailOTPRequest $request)
    {

        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->throwCustomValidationException(['message' => "The Email provided is invalid"]);
        }

        $mailOtpOneTime = MailOtp::where('email', $request->email)->where('verified', 0)->first();
        $mail_otp_exists =  MailOtp::where('email', $email)->exists();

        $mail_otp = mt_rand(100000, 999999);

        if($mailOtpOneTime != true){
            if($mail_otp_exists == false) {
                $mailOtp = MailOtp::create(['email' => $email,'otp' => $mail_otp]);
            }else{
                $mailOtp = MailOtp::where('email', $email)->first();
                $mailOtp->update(['otp' => $mail_otp,'verified' => 0]);
            }
        }

        $otp = [
            'name' => $user->name,
            'email' => $user->email,
            'otp' => $mailOtpOneTime ? $mailOtpOneTime->otp : $mailOtp->otp,
        ];

        if ($request->has('email')) {
            Mail::to($email)->send(new OtpMail($otp));
        }

        return $this->respondOk("An OTP has been resent to your email. Please check for the 6-digit code.");

    }

    public function validateEmailOTP(ValidateEmailOTPRequest $request)
    {
        $otp = $request->otp;
        $email = $request->email;

        $admin = User::where('id', 1)->first();
        $user = User::where('email', $email)->first();

        $verify_otp = MailOtp::where('email', $email)->where('otp', $otp)->exists();

        if (!$verify_otp) {
            $this->throwCustomValidationException(['message' => "The OTP provided is invalid"]);
        }

        $verify_otp_expire = MailOtp::where('email', $email)->where('otp', $otp)->where('verified', true)->first();

        if ($verify_otp_expire && $user->email_confirmed == 1) {
            $this->throwCustomValidationException(['message' => "The OTP provided has expired"]);
        }

        $owner = $user->owner()->where('email', $email)->first();
        // $adminDetail = AdminDetail::whereHas('user', function ($query) use ($email) {
        //     $query->where('email', $email);
        // })->first();

        if ($owner) {
            $data = [
                'name' => $user->name,
                'admin_name' => $admin->name,
                'owner_id' => $owner->owner_unique_id,
                'email' => $user->email,
                'is_approval' => $owner->approve,
            ];

            if ($email) {
                Mail::to($email)->send(new AdminRegister($data));
            }

            if ($admin->email) {
                Mail::to($admin->email)->send(new SuperAdminNotification($data));
            }
        }

        MailOtp::where('email', $email)->where('otp', $otp)->update(['verified' => true]);
        $user->update(['active' => true, 'email_confirmed' => true]);

        $deriver = Driver::whereHas('user', function ($query) use ($email) {
            $query->where('email', $email);
        })->first();

        if ($user->email_confirmed == 1 && $user->active == 1) {
            if ($deriver) {
                if($deriver->approve != 1) {
                    $this->throwCustomException('Your account is pending approval. Please wait for our team to review and approve your account.');
                }

                if($deriver->approve == 1){
                    return $this->loginUserAccountApp($request, Role::DRIVER);
                }

                return $this->loginUserAccountApp($request, Role::DRIVER);
            } elseif ($owner) {
                return $this->loginUserAccountApp($request, Role::OWNER);
            } else {
                return $this->loginUserAccountApp($request, Role::USER);
            }
        }

        // return response()->json(['success'=>true, 'message' => 'Your email has been verified. You can now login to your account.']);
    }

    public function register(UserRegistrationRequest $request)
    {
        $mobileUuid = $request->input('uuid');

        $country_id =  $this->country->where('dial_code', $request->input('country'))->pluck('id')->first();
        $validate_exists_email = $this->user->belongsTorole(Role::USER)->where('email', $request->email)->exists();

        if ($validate_exists_email) {

             if($request->is_web){

                $user = $this->user->belongsTorole(Role::USER)->where('email', $request->email)->first();

                return $this->authenticateAndRespond($user, $request, $needsToken=true);

            }
            $this->throwCustomException('Provided email has already been taken');
        }

        // $mobile = $this->otpHandler->getMobileFromUuid($mobileUuid);
        $mobile = $request->mobile;

        $validate_exists_mobile = $this->user->belongsTorole(Role::USER)->where('mobile', $mobile)->exists();

        if ($validate_exists_mobile) {

            if($request->is_web){

                $user = $this->user->belongsTorole(Role::USER)->where('mobile', $mobile)->first();

                return $this->authenticateAndRespond($user, $request, $needsToken=true);

            }
            $this->throwCustomException('Provided mobile has already been taken');
        }

        if (!$country_id) {
            $this->throwCustomException('unable to find country');
        }


        if ($request->has('refferal_code')) {
            // Validate Referral code
            $referred_user_record = $this->user->belongsTorole(Role::USER)->where('refferal_code', $request->refferal_code)->first();
            if (!$referred_user_record) {
                $this->throwCustomException('Provided Referral code is not valid', 'refferal_code');
            }
            // Add referral commission to the referred user
            $this->addCommissionToRefferedUser($referred_user_record);
        }

        $profile_picture = null;
        // if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request->profile_picture)) {
        //     $profile_picture = $this->imageUploader->file($uploadedFile)
        //         ->saveProfilePicture();
        // }
        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->file('profile_picture')->store('profile_picture', 'public');
            $user_params = $request->except('profile_picture');
            $user_params['profile_picture'] = $profile_picture;
        }

        if ($request->has('email_confirmed') == true)
        {
            $user_params['email_confirmed']= true;
        }
        // DB::beginTransaction();
        // try {
        $user_params = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile' => $mobile,
            'active' => 0,
            'mobile_confirmed' => true,
            'fcm_token'=>$request->input('device_token'),
            'login_by'=>$request->input('login_by'),
            'country'=>$country_id,
            'refferal_code'=>str_random(6),
            'profile_picture'=>$profile_picture,
            'lang'=>$request->input('lang')
        ];

        if($request->has('is_bid_app')){

            $user_params['is_bid_app']=1;
        }

        // if (env('APP_FOR')=='demo' && $request->has('company_key') && $request->input('company_key')) {
        //     $user_params['company_key'] = $request->input('company_key');
        // }
        if ($request->has('password') && $request->input('password')) {
            $user_params['password'] = bcrypt($request->input('password'));
        }
        $user = $this->user->create($user_params);

        // $this->otpHandler->delete($mobileUuid);

        // Create Empty Wallet to the user
        $user->userWallet()->create(['amount_added'=>0]);

        $user->attachRole(Role::USER);

        // $this->dispatch(new UserRegistrationNotification($user));

        // event(new UserRegistered($user));

        if ($request->has('oauth_token') & $request->input('oauth_token')) {
            $oauth_token = $request->oauth_token;
            $social_user = Socialite::driver($provider)->userFromToken($oauth_token);
            // Update User data with social provider
            $user->social_id = $social_user->id;
            $user->social_token = $social_user->token;
            $user->social_refresh_token = $social_user->refreshToken;
            $user->social_expires_in = $social_user->expiresIn;
            $user->social_avatar = $social_user->avatar;
            $user->social_avatar_original = $social_user->avatar_original;
            $user->save();
        }

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

        //     DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error($e);
        //     Log::error('Error while Registering a user account. Input params : ' . json_encode($request->all()));
        //     return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        // }
        // if(($user->email_confirmed) == true){

        // /*mail Template*/
        //     $user_name = $user->name;
        //     $mail_template = MailTemplate::where('mail_type', 'welcome_mail')->first();

        //     $description = $mail_template->description;

        //     $description = str_replace('$user_name', $user_name, $description);

        //     $mail_template->description = $description;

        //     $mail_template = $mail_template->description;

        //     $user_mail = $user->email;

        // // dispatch(new SendMailNotification($mail_template, $user_mail));

        // /*mail Template*/
        // }
        if ($user) {
            // return $this->authenticateAndRespond($user, $request, $needsToken=true);
            return $this->respondOk("Your account register successfully. Please check your email for 6 digit OTP");
        }
        return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');

        // return $this->respondSuccess();
    }


    public function validateUserMobile(Request $request)
    {
        $mobile = $request->mobile;

        $validate_exists_mobile = $this->user->belongsTorole(Role::USER)->where('mobile', $mobile)->exists();

        if ($validate_exists_mobile) {
            $this->throwCustomException('Provided mobile has already been taken');
        }

        return $this->respondSuccess(null, 'mobile_validated');
    }

     public function validateUserMobileForLogin(Request $request)
    {

      if ($request->has('mobile'))
        {
            $mobile = $request->mobile;

            $validate_exists_mobile = $this->user->belongsTorole(Role::USER)->where('mobile', $mobile)->exists();

            if ($validate_exists_mobile) {
                return $this->respondSuccess(null, 'mobile_exists');
            }

         return $this->respondFailed('mobile_does_not_exists');

        }

      if ($request->has('email'))
         {
                $email = $request->input('email');

            $validate_exists_email = $this->user->belongsTorole(Role::USER)->where('email', $email)->exists();
            if ($validate_exists_email)
            {
                return $this->respondSuccess(null, 'email_exists');
            }

         return $this->respondFailed('email_does_not_exists');

        }
    }

    /**
    * Add Commission to the referred user
    *
    */
    public function addCommissionToRefferedUser($reffered_user)
    {
        $user_wallet = $reffered_user->userWallet;
        $referral_commision = get_settings('referral_commision_for_user')?:0;

        $user_wallet->amount_added += $referral_commision;
        $user_wallet->amount_balance += $referral_commision;
        $user_wallet->save();

        // Add the history
        $reffered_user->userWalletHistory()->create([
            'amount'=>$referral_commision,
            'transaction_id'=>str_random(6),
            'remarks'=>WalletRemarks::REFERRAL_COMMISION,
            'refferal_code'=>$reffered_user->refferal_code,
            'is_credit'=>true]);

        // Notify user
        $title = trans('push_notifications.referral_earnings_notify_title');
        $body = trans('push_notifications.referral_earnings_notify_body');

        dispatch(new SendPushNotification($reffered_user,$title,$body));

    }


    /**
     * Send the mobile number verification OTP during registration.
     * @bodyParam country string required dial_code of the country
     * @bodyParam mobile int required Mobile of the user
     * @bodyParam email string required Email of the user
     * @param \App\Http\Requests\Auth\Registration\SendRegistrationOTPRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response {
     * "success":true,
     * "message":"success",
     * "data":{
     * "uuid":"6ffa38d1-d2ca-434a-8695-701ca22168b1"
     * }
     * }
     */
    public function sendOTP(SendRegistrationOTPRequest $request)
    {
        // dd(ceil(600.01 / 50) * 50);

        $field = 'mobile';

        $mobile = $request->input($field);

        DB::beginTransaction();
        try {
            $country_code = $this->country->where('dial_code', $request->input('country'))->exists();
            if (!$country_code) {
                $this->throwCustomValidationException('unable to find country', 'dial_code');
            }
            $mobileForOtp = $request->input('country') . $mobile;

            if (!$this->otpHandler->setMobile($mobile)->create()) {
                $this->throwSendOTPErrorException($field);
            }

            $otp = $this->otpHandler->getOtp();
            // Generate sms from template
            $sms = sms_template('generic-otp', ['otp'=>$otp,'mobile'=>$mobileForOtp], 'en');
            // Send sms by providers
            $this->smsContract->queueOn('default', $mobile, $sms);
            // $this->dispatch(new OtpNotification($mobile, $otp, $sms));

            /**
             * Send OTP here
             * Temporary logger
             */
            // Twilio::message($mobileForOtp, $message);

            \Log::info($sms);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            Log::error('Error while Registering a user account. Input params : ' . json_encode($request->all()));
            return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        }
        DB::commit();

        // return $this->respondSuccess(['uuid' => $this->otpHandler->getUuid()]);

        return response()->json(['success'=>true,'message'=>'success','message_keyword'=>'otp_sent_successfuly','data'=>['uuid' => $this->otpHandler->getUuid()]]);
    }

    /**
     * Validate the mobile number verification OTP during registration.
     * @bodyParam otp string required Provided otp
     * @bodyParam uuid uuid required uuid comes from sen otp api response
     *
     * @param \App\Http\Requests\Auth\Registration\ValidateRegistrationOTPRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response {"success":true,"message":"success"}
     */
    public function validateOTP(ValidateRegistrationOTPRequest $request)
    {
        $otpField = 'otp';
        $uuidField = 'uuid';

        $otp = $request->input($otpField);
        $uuid = $request->input($uuidField);

        if (!$this->otpHandler->validate($otp, $uuid)) {
            $message = $this->otpHandler->isExpired() ?
            'The otp provided has expired.' :
            'The otp provided is invalid.';

            $this->throwCustomValidationException($message, $otpField);
        }

        // return $this->respondSuccess();
        return response()->json(['success'=>true,'message'=>'success','message_keyword'=>'otp_validated_successfuly']);
    }
}
