<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Auth\SendLoginOTPRequest;
use App\Http\Requests\Auth\App\GenericAppLoginRequest;
use App\Http\Controllers\Web\Auth\LoginController as BaseLoginController;
use App\Models\Admin\Driver;
use App\Models\MailOtp;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\Registration\ValidateEmailOTPRequest;
use App\Mail\OtpMail;
use App\Models\Admin\Order;
use App\Models\Admin\Owner;
use App\Models\Admin\Subscription;

/**
 * @group Authentication
 *
 * APIs for Authentication
 */
class LoginController extends BaseLoginController
{
    /**
     * Login user and respond with access token and refresh token.
     * @group User-Login
     *
     * @param \App\Http\Requests\Auth\App\GenericAppLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @bodyParam email string optional email of the user entered
     * @bodyParam mobile string optional mobile of the user entered
     * @bodyParam password string optional password of the user entered
     * @bodyParam device_token string required fcm_token of the user entered

     * @response {
    "token_type": "Bearer",
    "expires_in": 1296000,
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjM4ZTE2N2YyNzlkM2UzZWEzODM5ZGNlMmY4YjdiNDQxYjMwZDQ0YmVlYjAzOWNmZjMzMmE2ZTc0ZDY1MDRiNmE3NjhhZWQzYWU5ZjE5MGUwIn0.eyJhdWQiOiIyIiwianRpIjoiMzhlMTY3ZjI3OWQzZTNlYTM4MzlkY2UyZjhiN2I0NDFiMzBkNDRiZWViMDM5Y2ZmMzMyYTZlNzRkNjUwNGI2YTc2",
    "refresh_token": "def5020045b028faaca5890136e3a8d7c850fb6b95cf2f78698b2356e544ee567cef1efa4099eaea3e3738ba11c9baabb1188a3d49de316e4451f32cdaa6017ebb9ff748fdf43d84b4e796a0456c4125ebaeca7930491fe315e4b86adf787999250966"
}
     */
    public function loginUser(GenericAppLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if($user->email_confirmed != 1) {
            $this->throwCustomException('Your account email has not been verified. Please verify your email to proceed.');
        }

        $mailOtpOneTime = MailOtp::where('email', $request->email)->where('verified', 0)->first();
        $mail_otp_exists =  MailOtp::where('email', $request->email)->exists();

        $mail_otp = mt_rand(100000, 999999);

        if($mailOtpOneTime != true){
            if($mail_otp_exists == false) {
                $mailOtp = MailOtp::create([
                    'email' => $request->email,
                    'otp' => $mail_otp,
                ]);
            }else{
                $mailOtp = MailOtp::where('email', $request->email)->first();
                $mailOtp->update(['otp' => $mail_otp, 'verified' => 0]);
            }
        }

        $otp = [
            'name' => $user->name,
            'email' => $user->email,
            'otp' => $mailOtpOneTime ? $mailOtpOneTime->otp : $mailOtp->otp,
        ];

        if ($request->has('email')) {
            Mail::to($user->email)->send(new OtpMail($otp));
        }

        // if($user->email_confirmed == 1){
        //     return $this->loginUserAccountApp($request, Role::USER);
        // }
        return $this->respondOk("Your OTP has been sent for login. Please check your email for the 6-digit code.");
    }

    public function loginValidateOTP(ValidateEmailOTPRequest $request)
    {
        $otp = $request->otp;
        $email = $request->email;

        $user = User::where('email', $email)->first();

        $verify_otp = MailOtp::where('email', $email)->where('otp', $otp)->exists();

        if (!$verify_otp) {
            $this->throwCustomValidationException(['message' => "The OTP provided is invalid"]);
        }

        $verify_otp_expire = MailOtp::where('email', $email)->where('otp', $otp)->where('verified', true)->first();

        if ($verify_otp_expire) {
            $this->throwCustomValidationException(['message' => "The OTP provided has expired"]);
        }

        MailOtp::where('email', $email)->where('otp', $otp)->update(['verified' => true]);

        if($user->email_confirmed == 1){
            return $this->loginUserAccountApp($request, Role::USER);
        }
    }

    public function loginDriver(GenericAppLoginRequest $request)
    {
        // if($request->has('role') && $request->role=='driver'){
        //     return $this->loginUserAccountApp($request, Role::DRIVER);
        // }

        // if($request->has('role') && $request->role=='admin'){
        //     return $this->loginUserAccountApp($request, Role::ADMIN);
        // }
        $registeredDriver = Driver::where('email', $request->input('email'))->first();

        $owner = Owner::whereHas('user', function ($query) use ($registeredDriver) {
            $query->where('owner_unique_id', $registeredDriver->owner_id);
        })->first();
        $packageExpiryDate = Order::where('user_id', $owner->user_id)->where('active', 2)->first();

        if ($packageExpiryDate) {
            $this->throwCustomException('Your company’s subscription has expired. Please renew your company package.');
        }

        $user = User::where('email', $request->email)->first();

        if($user->email_confirmed != 1) {
            $this->throwCustomException('Your account email has not been verified. Please verify your email to proceed.');
        }

        $validate_exists_email = User::belongsTorole([Role::ADMIN,Role::USER])->where('email', $request->email)->exists();

        if ($validate_exists_email) {
            $this->throwCustomException('The selected email is invalid.');
        }

        $user = User::where("email", $request->email)->first();

        $mailOtpOneTime = MailOtp::where('email', $request->email)->where('verified', 0)->first();
        $mail_otp_exists =  MailOtp::where('email', $request->email)->exists();

        $mail_otp = mt_rand(100000, 999999);

        if($mailOtpOneTime != true){
            if($mail_otp_exists == false) {
                $mailOtp = MailOtp::create([
                    'email' => $request->email,
                    'otp' => $mail_otp,
                ]);
            }else{
                $mailOtp = MailOtp::where('email', $request->email)->first();
                $mailOtp->update(['otp' => $mail_otp, 'verified' => 0]);
            }
        }

        $otp = [
            'name' => $user->name,
            'email' => $user->email,
            'otp' => $mailOtpOneTime ? $mailOtpOneTime->otp : $mailOtp->otp,
        ];

        if ($request->has('email')) {
            Mail::to($user->email)->send(new OtpMail($otp));
        }

        return $this->respondOk("Your OTP has been sent for login. Please check your email for the 6-digit code.");

        // if($deriver->approve == 1){
        //     return $this->loginUserAccountApp($request, Role::DRIVER);
        // }
    }

    public function driverLoginValidateOTP(ValidateEmailOTPRequest $request)
    {
        $otp = $request->otp;
        $email = $request->email;

        $user = User::where('email', $email)->first();

        $verify_otp = MailOtp::where('email', $email)->where('otp', $otp)->exists();

        if (!$verify_otp) {
            $this->throwCustomValidationException(['message' => "The OTP provided is invalid"]);
        }

        $verify_otp_expire = MailOtp::where('email', $email)->where('otp', $otp)->where('verified', true)->first();

        if ($verify_otp_expire) {
            $this->throwCustomValidationException(['message' => "The OTP provided has expired"]);
        }

        MailOtp::where('email', $email)->where('otp', $otp)->update(['verified' => true]);

        $deriver = Driver::where("user_id", $user->id)->where("email", $request->email)->first();

        if($deriver->approve != 1) {
            $this->throwCustomException('Your account is pending approval. Please wait for our team to review and approve your account.');
        }

        if($deriver->approve == 1){
            return $this->loginUserAccountApp($request, Role::DRIVER);
        }
    }

    public function loginAdmin(GenericAppLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if($user->email_confirmed != 1) {
            $this->throwCustomException('Your account email has not been verified. Please verify your email to proceed.');
        }

        $mailOtpOneTime = MailOtp::where('email', $request->email)->where('verified', 0)->first();
        $mail_otp_exists =  MailOtp::where('email', $request->email)->exists();

        $mail_otp = mt_rand(100000, 999999);

        if($mailOtpOneTime != true){
            if($mail_otp_exists == false) {
                $mailOtp = MailOtp::create([
                    'email' => $request->email,
                    'otp' => $mail_otp,
                ]);
            }else{
                $mailOtp = MailOtp::where('email', $request->email)->first();
                $mailOtp->update(['otp' => $mail_otp, 'verified' => 0]);
            }
        }

        $otp = [
            'name' => $user->name,
            'email' => $user->email,
            'otp' => $mailOtpOneTime ? $mailOtpOneTime->otp : $mailOtp->otp,
        ];

        if ($request->has('email')) {
            Mail::to($user->email)->send(new OtpMail($otp));
        }

        return $this->respondOk("Your OTP has been sent for login. Please check your email for the 6-digit code.");

        // if($user->email_confirmed == 1){
        //     return $this->loginUserAccountApp($request, Role::adminRoles());
        // }
    }

    public function adminLoginValidateOTP(ValidateEmailOTPRequest $request)
    {
        $otp = $request->otp;
        $email = $request->email;

        $user = User::where('email', $email)->first();

        $verify_otp = MailOtp::where('email', $email)->where('otp', $otp)->exists();

        if (!$verify_otp) {
            $this->throwCustomValidationException(['message' => "The OTP provided is invalid"]);
        }

        $verify_otp_expire = MailOtp::where('email', $email)->where('otp', $otp)->where('verified', true)->first();

        if ($verify_otp_expire) {
            $this->throwCustomValidationException(['message' => "The OTP provided has expired"]);
        }

        MailOtp::where('email', $email)->where('otp', $otp)->update(['verified' => true]);

        if($user->email_confirmed == 1){
            return $this->loginUserAccountApp($request, Role::ADMIN);
        }
    }

    public function socialAuth(Request $request, $provider)
    {
        $oauth_token = $request->oauth_token;
        $social_user = Socialite::driver($provider)->userFromToken($oauth_token);

        $user = User::where('social_provider', $provider)->where('social_id', $social_user->id)->first();

        if (!$user) {
            $this->throwCustomException('user-not-found');
        }
        // Update User data with social provider
        $user->social_id = $social_user->id;
        $user->social_token = $social_user->token;
        $user->social_refresh_token = $social_user->refreshToken;
        $user->social_expires_in = $social_user->expiresIn;
        $user->social_avatar = $social_user->avatar;
        $user->social_avatar_original = $social_user->avatar_original;
        $user->login_by = $request->input('login_by');
        $user->fcm_token = $request->input('device_token')?:null;
        $user->save();
        $client_tokens = DB::table('oauth_clients')->where('personal_access_client', 1)->first();

        return $this->issueToken([
                'grant_type' => 'personal_access',
                'client_id' => $client_tokens->id,
                'client_secret' => $client_tokens->secret,
                'user_id' => $user->id,
                'scope' => [],
            ]);
    }


    /**
     * Login Dispatcher user and respond with access token and refresh token.
     * @group User-Login
     *
     * @param \App\Http\Requests\Auth\App\GenericAppLoginRequest $request
     * @return \Illuminate\Http\JsonResponse

     * @response {
    "token_type": "Bearer",
    "expires_in": 1296000,
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjM4ZTE2N2YyNzlkM2UzZWEzODM5ZGNlMmY4YjdiNDQxYjMwZDQ0YmVlYjAzOWNmZjMzMmE2ZTc0ZDY1MDRiNmE3NjhhZWQzYWU5ZjE5MGUwIn0.eyJhdWQiOiIyIiwiacaP8zkCWTpzh8ZtWBUYVrPkYRWbwz-L5x6dx2d901Aq_7-LwlzPMtP0N93kVfFuLwK2RCzlVtcCTxZaUW9S7x3Y",
    "refresh_token": "def5020045b028faaca5890136e3a8d7c850fb6b95cf2f78698b2356e544ee567cef1efa4099eaea3e3738ba11c9baabb1188a3d49de316e4451f32cdaa6017ebb9ff748fdf43d84b4e796a0456c4125ebaeca7930491fe315e4b86adf7879992509667dd68eacc488bddb2cc005357cdab1da5f0582659eef11e06bf2447c1209f6c17c83453cd6fa6dd6d5d98ff7129a6d3f3509c6c99fba379ea4aee85c0eb89b5f648682484452219d1c592d80c3165657a519f790ba19ad347774c0a199"
}*/
    public function loginDispatcher(GenericAppLoginRequest $request)
    {
        return $this->loginUserAccountApp($request, Role::DISPATCHER);
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
    * Obtain the user information from GitHub.
    *
    * @return \Illuminate\Http\Response
    */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('facebook')->user();

        // $user->token;
    }


    /**
     * Logout the user based on their access token.
     * @group User-Login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @response {"success":true,"message":"success"}
     */
    public function logout(Request $request)
    {
        $user = auth()->user();

        $user->fcm_token=null;
        $user->save();

        auth()->user()->token()->revoke();

        return $this->respondSuccess();
    }

    /**
     * Send the OTP for user login.
     * @group User-Login
     * @param \App\Http\Requests\Auth\SendLoginOTPRequest $request
     * @bodyParam mobile string required mobile of the user entered
     * @return \Illuminate\Http\JsonResponse
     * @response {"success":true,"message":"success","uuid":"54e4ebe54er5e45re5ber54r5r5rr"}
     */
    public function sendUserLoginOTP(SendLoginOTPRequest $request)
    {
        $field = 'mobile';

        $mobile = $request->input($field);

        $user = $this->resolveUserFromMobile($mobile, Role::USER);

        $this->validateUser($user, "User with that mobile number doesn't exist.", $field);

        if (!$user->createOTP()) {
            $this->throwSendOTPErrorException($field);
        }

        $otp = $user->getCreatedOTP();
        /**
        * Send OTP here
        * Temporary logger
        */
        \Log::info("Login OTP for {$mobile} is : {$otp}");

        return $this->respondSuccess(['uuid' => $user->getCreatedOTPUuid()]);
    }

    /**
     * Validate the user model and their account status.
     *
     * @param \App\Models\User|null $user
     * @param string $message
     * @param string|null $field
     */
    protected function validateUser($user, $message, $field = null)
    {
        if (!$user) {
            $this->throwCustomException($message, $field);
        }

        if (!$user->isActive()) {
            $this->throwAccountDisabledException($field);
        }
    }
}
