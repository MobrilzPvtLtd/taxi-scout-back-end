<?php

namespace App\Http\Controllers\Api\V1\Auth\Password;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\Password\ForgotPasswordRequest;
use App\Http\Requests\Auth\Password\ResetPasswordRequest;
use App\Http\Requests\Auth\Password\ValidateResetTokenRequest;
use App\Jobs\Notifications\Auth\Password\PasswordResetNotification;
use App\Mail\ForgotPassword;
use App\Models\User;
use Mail;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\DB;

/**
 * @group Password-Reset
 *
 * APIs for Email-Management
 */
class PasswordResetController extends ApiController
{
    /**
     * The user model instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * The password broker instance.
     *
     * @var \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected $broker;

    /**
     * PasswordResetController constructor.
     *
     * @param \App\Models\User $user
     * @param \Illuminate\Contracts\Auth\PasswordBroker $broker
     */
    public function __construct(User $user, PasswordBroker $broker)
    {
        $this->user = $user;
        $this->broker = $broker;
    }

    /**
     * Send the password reset email to the user.
     *
     * @param \App\Http\Requests\Auth\Password\ForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @bodyParam email email required email of the user entered
     * @response {"success":true,"message":"success"}
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        if ($request->has('email')) {
            $email = $request->input('email');

            $user = $this->resolveUserFromEmail($email);
        }

        if ($request->has('mobile')) {
            $mobile = $request->input('mobile');

            $user = $this->resolveUserFromMobile($mobile);
        }

        if (!$request->has('mobile')&&!$request->has('email')) {
            return $this->respondBadRequest('Input Params could be mismatched.');
        }

        if (!$user) {
            $this->throwCustomValidationException("We can't find a user with that email address.", 'email');
        }

        if (!$user->isActive()) {
            $this->throwAccountDisabledException('email');
        }

        $otp = mt_rand(100000, 999999);
        $email = $user->email;

        $existingReset = DB::table('password_resets')->where('email', $email)->first();

        if ($existingReset) {
            DB::table('password_resets')
                ->where('email', $email)
                ->update(['token' => $otp]);
        } else {
            DB::table('password_resets')->insert([
                'token' => $otp,
                'email' => $email,
            ]);
        }

        if ($request->has('email')) {
            // $this->dispatch(new PasswordResetNotification($user, $this->broker->createToken($user)));
            Mail::to($user->email)->send(new ForgotPassword($user, $otp));
            return $this->respondSuccess(null, "Please check your email for the 6-digit OTP.");
        }
    }

    /**
     * Validate the password reset token.
     *
     * @param *\App\Http\Requests\Auth\Password\ValidateResetTokenRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @bodyParam token string required token of the email
     * @bodyParam email string required email of the user entered
     * @response {"success":true,"message":"success"}
     */
    public function validateToken(ValidateResetTokenRequest $request)
    {
        $token = $request->input('token');
        $email = $request->input('email');

        $this->validateResetToken($email, $token);

        return $this->respondSuccess();
    }

    /**
    * Validate the password reset token and update the password.
    * @bodyParam mobile number required number of the user entered
    * @bodyParam role required string of the user entered
    * @param \App\Http\Requests\Auth\Password\ResetPasswordRequest $request
    * @return \Illuminate\Http\JsonResponse
    * @response {"success":true,"message":"reset-success"}
    */
    public function reset(ResetPasswordRequest $request)
    {
        $password = $request->input('password');


        if ($request->has('email')&&$request->has('otp')) {
            $existingReset = DB::table('password_resets')
                            ->where('email', $request->input('email'))
                            ->where('token', $request->input('otp'))
                            ->first();
            // $user = $this->validateResetToken($email, $otp);
            if (!$existingReset) {
                return $this->respondBadRequest('Invalid OTP !');
            }
        }

        if ($request->has('mobile') && $request->has('role')) {
            $mobile = $request->input('mobile');
            $user = $this->resolveUserFromMobile($mobile, $request->role);
        }

        if (!$request->has('mobile')&&!$request->has('email')) {
            return $this->respondBadRequest('Input Params could be mismatched.');
        }

        // $existingReset->forceFill([
        //     'password' => bcrypt($password),
        //     'remember_token' => null,
        // ])->save();

        DB::table('users')->where('email', $request->has('email'))
                ->update(['password' => bcrypt($password)]);

        if ($request->has('email')) {
            $password = $request->input('password');
            DB::table('users')->where('email', $request->input('email'))
                ->update(['password' => bcrypt($password)]);

            DB::table('password_resets')->where('email', $request->input('email'))->delete();
        }

        return $this->respondSuccess(null, 'reset-success');
    }

    /**
     * Validate the email, token and return the user.
     *
     * @param string $email
     * @param string $token
     * @return \App\Models\User
     */
    protected function validateResetToken($email, $token)
    {
        $user = $this->resolveUserFromEmail($email);

        if (!$user || !$this->broker->tokenExists($user, $token)) {
            $this->throwCustomValidationException(
                'The password reset token is invalid or has expired.',
                'email'
            );
        }

        return $user;
    }

    /**
     * Resolve the user from their email.
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    protected function resolveUserFromEmail($email)
    {
        return $this->user->doesNotBelongToRole($this->rolesUsingUsername())
            ->where('email', $email)
            ->first();
    }
}
