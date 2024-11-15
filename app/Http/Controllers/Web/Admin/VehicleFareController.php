<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Constants\Masters\zoneRideType;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Zone\AssignZoneTypeRequest;
use App\Models\Admin\VehicleType;
use App\Models\Admin\Zone;
use App\Models\Admin\ZoneType;
use App\Models\Admin\ZoneTypePackagePrice;
use App\Models\Admin\ZoneTypePrice;
use Illuminate\Http\Request;
use App\Base\Constants\Auth\Role as RoleSlug;
use App\Models\Admin\Owner;
use App\Models\Admin\Order;

class VehicleFareController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('owner')) {
            $packageExpiryDate = Order::where('user_id', auth()->user()->id)->where('active', 2)->first();
            if($packageExpiryDate){
                return redirect('/order');
            }
        }
        $page = trans('pages_names.vehicle-fare');
        $main_menu = 'vehicle-fare';
        $sub_menu = '';

        return view('admin.vehicle_fare.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetchFareList(QueryFilterContract $queryFilter)
    {
        if (auth()->user()->hasRole('owner')) {
            $packageExpiryDate = Order::where('user_id', auth()->user()->id)->where('active', 2)->first();
            if($packageExpiryDate){
                return redirect('/order');
            }
        }
        // dd($queryFilter);
        if (access()->hasRole(RoleSlug::SUPER_ADMIN)) {
            $query = ZoneTypePrice::latest();
        }else{
            $query = ZoneTypePrice::where('owner_id', auth()->user()->owner->owner_unique_id)->latest();
        }

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return view('admin.vehicle_fare._fare_list', compact('results'));
    }

    public function create()
    {
        if (auth()->user()->hasRole('owner')) {
            $packageExpiryDate = Order::where('user_id', auth()->user()->id)->where('active', 2)->first();
            if($packageExpiryDate){
                return redirect('/order');
            }
        }
        $zones = Zone::active()->get();

        // dd($zones);
        $page = trans('pages_names.add_vehicle_fare');
        $main_menu = 'vehicle-fare';
        $sub_menu = '';

        $admin = Owner::whereHas('user', function ($query) {
            $query->whereNotNull('owner_unique_id');
        })->get();

        return view('admin.vehicle_fare.create', compact('page', 'main_menu', 'sub_menu', 'zones','admin'));
    }

    public function fetchVehiclesByZone(Request $request)
    {
        $zone = Zone::whereId($request->_zone)->first();
        $ids = $zone->zoneType()->pluck('type_id')->toArray();
        // if($request->transport_type!='both'){
        //     $types = VehicleType::whereNotIn('id', $ids)->active()->where(function($query)use($request){
        //     $query->where('is_taxi',$request->transport_type);
        //     // ->orWhere('is_taxi','both');
        //     if (!access()->hasRole(RoleSlug::SUPER_ADMIN)) {
        //         $query->where('owner_id',auth()->user()->owner->owner_unique_id);
        //     }
        // })->get();
        // }else{
        //     $types = VehicleType::whereNotIn('id', $ids)->active()->get();
        // }

        if (access()->hasRole(RoleSlug::SUPER_ADMIN)) {
            $owner_id = request()->owner_id;
        }else{
            $owner_id = auth()->user()->owner->owner_unique_id;
        }

        $types = VehicleType::whereNotIn('id', $ids)->active()->whereIsTaxi("taxi")->where('vehicle_types.owner_id', $owner_id)->get();

        return response()->json(['success' => true, 'data' => $types]);
    }

    /*fetchTriptype*/
    public function fetchTriptype(Request $request)
    {
        $types = VehicleType::where('id', $request->selectedType)->first();
        return response()->json(['success' => true, 'data' => $types]);
    }

    public function store(AssignZoneTypeRequest $request)
    {
        if (auth()->user()->hasRole('owner')) {
            $packageExpiryDate = Order::where('user_id', auth()->user()->id)->where('active', 2)->first();
            if($packageExpiryDate){
                return redirect('/order');
            }
        }
        $zone  = Zone::whereId($request->zone)->first();
        $payment = implode(',', $request->payment_type);
        // To save default type

        if ($request->transport_type == 'taxi')
        {
            if ($zone->default_vehicle_type == null) {
                $zone->default_vehicle_type = $request->type;
                $zone->save();
            }
        }else{
            if ($zone->default_vehicle_type_for_delivery == null) {
                $zone->default_vehicle_type_for_delivery = $request->type;
                $zone->save();
            }
        }

        if (access()->hasRole(RoleSlug::SUPER_ADMIN)) {
            $ownerId = $request->owner_id;
        }else{
            $ownerId = auth()->user()->owner->owner_unique_id;
        }

        $zoneType = $zone->zoneType()->create([
            'type_id' => $request->type,
            'payment_type' => $payment,
            'transport_type' => $request->transport_type,
            'bill_status' => true
        ]);

        $zoneType->zoneTypePrice()->create([
            'owner_id' => $ownerId ?? null,
            'price_type' => zoneRideType::RIDENOW,
            'base_price' => $request->ride_now_base_price,
            'price_per_distance' => $request->ride_now_price_per_distance ?? 0.00,
            'cancellation_fee' => $request->ride_now_cancellation_fee,
            'base_distance' => $request->ride_now_base_distance ? $request->ride_now_base_distance : 0,
            'price_per_time' => $request->ride_now_price_per_time ? $request->ride_now_price_per_time : 0.00,
             'waiting_charge' => $request->ride_now_waiting_charge ? $request->ride_now_waiting_charge : 0.00,
            'free_waiting_time_in_mins_before_trip_start' =>  $request->ride_now_free_waiting_time_in_mins_before_trip_start ? $request->ride_now_free_waiting_time_in_mins_before_trip_start:0,
            'free_waiting_time_in_mins_after_trip_start' =>  $request->ride_now_free_waiting_time_in_mins_after_trip_start ? $request->ride_now_free_waiting_time_in_mins_after_trip_start:0,
        ]);

        $message = trans('Type assigned succesfully.');

        return redirect('vehicle_fare')->with('success', $message);
    }

    public function getById(ZoneTypePrice $zone_price)
    {
        if (auth()->user()->hasRole('owner')) {
            $packageExpiryDate = Order::where('user_id', auth()->user()->id)->where('active', 2)->first();
            if($packageExpiryDate){
                return redirect('/order');
            }
        }
        // dd($zone_price);
        $page = trans('pages_names.edit_vehicle_fare');
        $main_menu = 'vehicle-fare';
        $sub_menu = '';

        $admin = Owner::whereHas('user', function ($query) {
            $query->whereNotNull('owner_unique_id');
        })->first();
        // dd($admin);

        // dd($zone_price->zoneType->transport_type);
        return view('admin.vehicle_fare.edit', compact('page', 'main_menu', 'sub_menu', 'zone_price','admin'));
    }

    public function update(Request $request,ZoneTypePrice $zone_price)
    {
        if (auth()->user()->hasRole('owner')) {
            $packageExpiryDate = Order::where('user_id', auth()->user()->id)->where('active', 2)->first();
            if($packageExpiryDate){
                return redirect('/order');
            }
        }
        $zone_price->zoneType()->update([
            'type_id' => $request->type,
            'payment_type' => implode(',', $request->payment_type),
            'transport_type' => $request->transport_type,
        ]);
        if($zone_price->price_type == 1)
        {
        $zone_price->update([
            'base_price' => $request->ride_now_base_price,
            'price_per_distance' => $request->ride_now_price_per_distance ?? 0.00,
            'cancellation_fee' => $request->ride_now_cancellation_fee,
            'base_distance' => $request->ride_now_base_distance ? $request->ride_now_base_distance : 0,
            'price_per_time' => $request->ride_now_price_per_time ? $request->ride_now_price_per_time : 0.00,
             'waiting_charge' => $request->ride_now_waiting_charge ? $request->ride_now_waiting_charge : 0.00,
            'free_waiting_time_in_mins_before_trip_start' =>  $request->ride_now_free_waiting_time_in_mins_before_trip_start ? $request->ride_now_free_waiting_time_in_mins_before_trip_start:0,
            'free_waiting_time_in_mins_after_trip_start' =>  $request->ride_now_free_waiting_time_in_mins_after_trip_start ? $request->ride_now_free_waiting_time_in_mins_after_trip_start:0,
        ]);
        }
        $message = trans('Type fare updated succesfully.');

        return redirect('vehicle_fare')->with('success', $message);
    }

    public function toggleStatus(ZoneTypePrice $zone_price) {
        $status = $zone_price->zoneType->isActive() ? false : true;
        $zone_price->zoneType->update(['active' => $status]);

        $message = trans('Type fare status updated succesfully.');

        return redirect('vehicle_fare')->with('success', $message);
    }
    public function delete(ZoneTypePrice $zone_price)
    {
        if(env('APP_FOR')=='demo'){

        $message = 'you cannot delete the Vehicle fare. this is demo version';

        return $message;

        }

        $zone_type = ZoneType::where('id', $zone_price->zone_type_id)->get();
        // $package = ZoneTypePackagePrice::where('zone_type_id', $zone_price->zone_type_id)->get();

        foreach($zone_type as $type)
        {
          $type->delete();
          $type->zoneTypePrice()->delete();
        //   $package->delete();

        }
        $message = trans('Vehicle fare deleted succesfully.');

        return $message;
    }


    public function getTransportTypes(Request $request)
    {
        // $type = ['taxi','delivery','both'];
        $type = ['taxi'];
        return $type;
    }
}
