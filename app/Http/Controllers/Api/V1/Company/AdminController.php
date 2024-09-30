<?php
namespace App\Http\Controllers\Api\V1\Company;


use App\Http\Controllers\ApiController;
use App\Http\Controllers\Web\BaseController;
use App\Http\Requests\Admin\Driver\CreateDriverRequest;
use App\Http\Requests\Admin\Driver\UpdateDriverRequest;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Models\Admin\AdminDetail;
use App\Base\Constants\Auth\Role as RoleSlug;
use App\Models\User;
use App\Models\Admin\Driver;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Http\Requests\Admin\AdminDetail\CreateAdminRequest;
use App\Http\Requests\Admin\AdminDetail\UpdateAdminRequest;
use App\Http\Requests\Admin\AdminDetail\UpdateProfileRequest;
use App\Models\Admin\Company;
use App\Models\Country;
use App\Models\Access\Role;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin\Order;
use App\Models\Admin\Subscription;
use App\Mail\AdminRegister;
use App\Mail\OtpMail;
use App\Mail\SuperAdminNotification;
use App\Models\Admin\ServiceLocation;
use App\Models\MailOtp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * @resource Driver
 *
 * vechicle types Apis
 */
class AdminController extends BaseController
{
    /**
     * The Driver model instance.
     *
     * @var \App\Models\Admin\AdminDetail
     */
    protected $admin_detail;

    /**
     * The User model instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * The
     *
     * @var App\Base\Services\ImageUploader\ImageUploaderContract
     */
    protected $imageUploader;


    /**
     * DriverController constructor.
     *
     * @param \App\Models\Admin\AdminDetail $admin_detail
     */
    public function __construct(AdminDetail $admin_detail, ImageUploaderContract $imageUploader, User $user)
    {
        $this->admin_detail = $admin_detail;
        $this->imageUploader = $imageUploader;
        $this->user = $user;
    }

    /**
    * Get all admins
    * @return \Illuminate\Http\JsonResponse
    */
    public function index()
    {
		print_r('test'); exit;
    }

    public function getAllAdmin(QueryFilterContract $queryFilter)
    {
        $url = request()->fullUrl(); //get full url

        if (access()->hasRole(RoleSlug::SUPER_ADMIN)) {
            $query = AdminDetail::query();
            if (env('APP_FOR')=='demo') {
                $query = AdminDetail::whereHas('user', function ($query) {
                    $query->where('company_key', auth()->user()->company_key);
                });
            }
        } else {
            $this->validateAdmin();
            $query = $this->admin_detail->where('created_by', $this->user->id);
        }

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.admin._admin', compact('results'));
    }

    /**
    * Create Admins View
    *
    */
    public function create()
    {
        $countries = Country::active()->get();
        $services = ServiceLocation::companyKey()->active()->get();

        $main_menu = 'admin';
        $sub_menu = null;
        dd($services);
       // return view('admin.admin.create', compact('admins', 'page', 'countries', 'main_menu', 'sub_menu', 'roles', 'services'));
    }

    /**
     * Store admin.
     *
     * @param \App\Http\Requests\Admin\Driver\CreateDriverRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateAdminRequest $request)
    {
        $created_params = $request->only(['service_location_id', 'password','company_name','name','mobile','email','address','state','city','country']);

        $created_params['postal_code'] = $request->postal_code;
        $created_params['owner_name'] = $request->name;
        $created_params['password'] = bcrypt($request->input('password'));

        $created_params['active'] = true;
        $created_params['approve'] = true;
        if ($request->input('service_location_id')) {
            $timezone = ServiceLocation::where('id', $request->input('service_location_id'))->pluck('timezone')->first();
        } else {
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }

        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->file('profile_picture')->store('profile_picture', 'public');
            $user_params = $request->except('profile_picture');
            $user_params['profile_picture'] = $profile_picture;
        }

        $uuid = substr(Uuid::uuid4()->toString(), 0, 10);
        $created_params['owner_unique_id'] = $uuid;
        // try {
            $user_params = ['name'=>$request->input('name').' '.$request->input('last_name'),
                'email'=>$request->input('email'),
                'mobile'=>$request->input('mobile'),
                'mobile_confirmed'=>true,
                'active'=>false,
                'timezone'=>$timezone,
                'country'=>$request->input('country'),
                'password' => bcrypt($request->input('password')),
                // 'company_kye' => $uuid,
                'profile_picture'=>$profile_picture ?? '',
            ];

            if (env('APP_FOR')=='demo') {
                $user_params['company_key'] = '';
            }

            $subscription = Subscription::where('id', $request->package_id)->first();

            if (!$subscription) {
                return $this->respondBadRequest('The package_id field is required.');
            }

            $user = $this->user->create($user_params);

            $user->attachRole(RoleSlug::OWNER);

            $user->owner()->create($created_params);

            $user->owner->ownerWalletDetail()->create(['amount_added'=>0]);

            // $admin = User::where('id', 1)->first();
            // $userUuid = User::where('id', $user->id)->first();

            if ($subscription) {
                $start_date = Carbon::now();
                $end_date = $start_date->clone()->addDays($subscription->validity);
            }

            // $adminDetail = AdminDetail::where('user_id', $user->id)->first();
            // if ($adminDetail) {
            //     $adminDetail->is_approval = !$adminDetail->is_approval;
            //     $adminDetail->save();
            // }

            Order::create([
                'package_id' => $request->package_id,
                'user_id' => $user->id,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);

            // $data = [
            //     'name' => $user->name,
            //     'admin_name' => $admin->name,
            //     'company_key' => $userUuid->company_key,
            //     'email' => $user->email,
            //     'is_approval' => $adminDetail->is_approval,
            // ];

            // // $this->dispatch(new UserRegistrationNotification($user));
            // if ($request->has('email')) {
            //     Mail::to($user->email)->send(new AdminRegister($data));
            // }

            // if ($admin->email) {
            //     Mail::to($admin->email)->send(new SuperAdminNotification($data));
            // }

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

            if ($user) {
                return $this->respondOk("Your account register successfully. Please check your email for 6 digit OTP");
            }

            return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
            // $message = trans('succes_messages.company_added_succesfully');
		    // return $this->respondOk($message);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error($e . 'Error while Create Admin. Input params : ' . json_encode($request->all()));
        //     return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        // }
    }

    public function getById(AdminDetail $admin)
    {
        $page = trans('pages_names.edit_admin');

        if (access()->hasRole(RoleSlug::SUPER_ADMIN)) {
            $roles = Role::whereIn('slug', RoleSlug::adminRoles())->get();
        } else {
            $this->validateAdmin();
            $roles = Role::whereIn('slug', RoleSlug::adminRoles())->get();
        }
        $services = ServiceLocation::active()->get();
        $countries = Country::active()->get();
        $item = $admin;
        $main_menu = 'admins';
        $sub_menu = null;

        return view('admin.admin.update', compact('item', 'services', 'page', 'countries', 'main_menu', 'sub_menu', 'roles'));
    }


    public function update(AdminDetail $admin, UpdateAdminRequest $request)
    {
        if (env('APP_FOR')=='demo') {
            $message = trans('succes_messages.you_are_not_authorised');

            return redirect('admins')->with('warning', $message);
        }

        $updatedParams = $request->only(['service_location_id', 'first_name', 'last_name','mobile','email','address','state','city','country']);
        $updatedParams['pincode'] = $request->postal_code;


        $updated_user_params = ['name'=>$request->input('first_name').' '.$request->input('last_name'),
            'email'=>$request->input('email'),
            'mobile'=>$request->input('mobile'),
            'password' => bcrypt($request->input('password'))
        ];

        if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
            $updated_user_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }

        $admin->user->update($updated_user_params);

        $admin->user->roles()->detach();

        $admin->user->attachRole($request->role);

        $admin->update($updatedParams);

        $message = trans('succes_messages.admin_updated_succesfully');
        return redirect('admins')->with('success', $message);
    }
    public function toggleStatus(User $user)
    {
        if (env('APP_FOR')=='demo') {
            $message = trans('succes_messages.you_are_not_authorised');

            return redirect('admins')->with('warning', $message);
        }

        $status = $user->isActive() ? false: true;
        $user->update(['active' => $status]);

        $message = trans('succes_messages.admin_status_changed_succesfully');
        return redirect('admins')->with('success', $message);
    }

    public function delete(User $user)
    {
        if(env('APP_FOR')=='demo'){

        $message = 'you cannot perform this action due to demo version';

        return $message;

        }
        $user->delete();

        $message = trans('succes_messages.admin_deleted_succesfully');

        return $message;
        // return redirect('admins')->with('success', $message);
    }

    public function viewProfile(User $user)
    {
        $page = trans('pages_names.admins');

        $main_menu = 'admin';
        $sub_menu = null;

        return view('admin.admin.profile', compact('page', 'main_menu', 'sub_menu', 'user'));
    }

    public function updateProfile(UpdateProfileRequest $request, User $user)
    {
        if(env('APP_FOR')=='demo'){

        $message = 'you cannot update the profile due to demo version';

        return redirect('admins')->with('success', $message);

        }

        if ($request->action == 'password') {
            $updated_user_params['password'] = bcrypt($request->input('password'));
        } else {
            $updatedParams = $request->only(['first_name','mobile','email','address']);

            $updated_user_params = ['name'=>$request->input('first_name'),
                'email'=>$request->input('email'),
                'mobile'=>$request->input('mobile')
            ];

            if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
                $updated_user_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
                    ->saveProfilePicture();
            }

            $user->admin->update($updatedParams);
        }

        $user->update($updated_user_params);

        $message = trans('succes_messages.admin_profile_updated_succesfully');
        return redirect('admins')->with('success', $message);
    }
}
