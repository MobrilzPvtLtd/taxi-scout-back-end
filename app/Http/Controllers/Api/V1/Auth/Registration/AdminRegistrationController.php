<?php

namespace App\Http\Controllers\Api\V1\Auth\Registration;

use App\Base\Constants\Auth\Role;
use App\Events\Auth\UserRegistered;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\Registration\AdminRegistrationRequest;
use App\Http\Requests\Auth\Registration\UserRegistrationRequest;
use App\Jobs\Notifications\Auth\Registration\UserRegistrationNotification;
use App\Mail\AdminRegister;
use App\Mail\SuperAdminNotification;
use App\Models\User;
use App\Models\Admin\AdminDetail;
use App\Models\Admin\Order;
use App\Models\Admin\Subscription;
use Carbon\Carbon;
use DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminRegistrationController extends ApiController
{

    /**
     * The user model instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * The user model instance.
     *
     * @var \App\Models\User
     */
    protected $admin_detail;

    /**
     * AdminRegistrationController constructor.
     *
     * @param \App\Models\User $user
     */
    public function __construct(User $user, AdminDetail $admin_detail)
    {
        $this->user = $user;
        $this->admin_detail = $admin_detail;
    }

    /**
     * Register the admin user.
     * @hideFromAPIDocumentation
     * @param \App\Http\Requests\Auth\Registration\UserRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function packages(){
        return Subscription::get();
    }

    public function register(AdminRegistrationRequest $request)
    {
        DB::beginTransaction();
        try {
            $uuid = substr(Uuid::uuid4()->toString(), 0, 10);
            $name = $request->input('first_name').' '.$request->input('last_name');
            $user = $this->user->create([
                'name' => $name,
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
                'mobile' => $request->input('mobile'),
                'mobile_confirmed' => true,
                'company_key' => $uuid,
            ]);

            $admin_data = $request->only(['first_name', 'last_name', 'address', 'country','pincode','timezone','email','mobile','emergency_contact','area_name']);

            $admin = $user->admin()->create($admin_data);

            $user->attachRole(Role::ADMIN);

            event(new UserRegistered($user));

            $admin = User::where('id', 1)->first();
            $userUuid = User::where('id', $user->id)->first();
            $subscription = Subscription::where('id', $request->package_id)->first();

            if ($subscription) {
                $start_date = $subscription->created_at;
                $end_date = $start_date->clone()->addDays(30);
            }

            $adminDetail = AdminDetail::where('user_id', $user->id)->first();
            if ($adminDetail) {
                $adminDetail->is_approval = !$adminDetail->is_approval;
                // $adminDetail->package_id = $request->package_id;
                $adminDetail->save();
            }

            Order::create([
                'package_id' => $request->package_id,
                'user_id' => $user->id,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);

            $data = [
                'name' => $user->name,
                'admin_name' => $admin->name,
                'company_key' => $userUuid->company_key,
                'email' => $user->email,
                'is_approval' => $adminDetail->is_approval,
            ];

            // $this->dispatch(new UserRegistrationNotification($user));
            if ($request->has('email')) {
                Mail::to($user->email)->send(new AdminRegister($data));
            }

            if ($admin->email) {
                Mail::to($admin->email)->send(new SuperAdminNotification($data));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e . 'Error while Create Admin. Input params : ' . json_encode($request->all()));
            return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        }
        DB::commit();

        return $this->respondSuccess();
    }
}
