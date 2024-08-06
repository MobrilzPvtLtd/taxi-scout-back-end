<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Models\Admin\Driver;
use Illuminate\Http\Request;
use App\Models\Admin\VehicleType;
use App\Http\Controllers\Api\V1\BaseController;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Models\Admin\Owner;

/**
 * @group Vehicle Management
 *
 * APIs for Vehicle-Types
 */
class VehicleTypeController extends BaseController
{
    /**
     * The VehicleType model instance.
     *
     * @var \App\Models\Admin\VehicleType
     */
    protected $vehicle_type;

    /**
     * VehicleTypeController constructor.
     *
     * @param \App\Models\Admin\VehicleType $vehicle_type
     */
    public function __construct(VehicleType $vehicle_type, ImageUploaderContract $imageUploader)
    {
        $this->vehicle_type = $vehicle_type;
        $this->imageUploader = $imageUploader;
    }

    public function getVehicleTypesByCompanyId($company_id)
    {
        $owner = Owner::where('owner_unique_id', $company_id)->first();

        $response = $this->vehicle_type->whereActive(true)->where('owner_id', $owner->owner_unique_id)->get();

        return $this->respondSuccess($response);
    }
}
