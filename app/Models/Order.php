<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_order',
        'economic_number',
        'order_file',
        'vehicle_dependency_id',
        'vehicle_vin',
        'vehicle_description',
        'vehicle_model',
        'vehicle_plate',
        'service_type_id',
        'service_requested_date',
        'service_location_id',
        'service_description',
        'appointment',
        'appointment_date',
        'appointment_workshop_id',
    ];

    /**
     * The attributes that are appended when model is retrieved
     *
     * @var array<int, string>
     */
    protected $appends = [
        'vehicle_dependency',
        'service_type',
        'service_location',
        'appointment_workshop',
    ];

    /**
     * Get the order vehicle's dependency name
     */
    protected function getVehicleDependencyAttribute()
    {
        $dependency = Dependency::find($this->vehicle_dependency_id);

        return empty($dependency) ? 'N/D' : $dependency->name;
    }

    /**
     * Get the order's service type name
     */
    protected function getServiceTypeAttribute()
    {
        $service = Service::find($this->service_type_id);

        return empty($service) ? 'N/D' : $service->name;
    }

    /**
     * Get the order's location name
     */
    protected function getServiceLocationAttribute()
    {
        $location = Location::find($this->service_location_id);

        return empty($location) ? 'N/D' : $location->name;
    }

    /**
     * Get the order appointment's workshop name
     */
    protected function getAppointmentWorkshopAttribute()
    {
        $workshop = Workshop::find($this->appointment_workshop_id);

        return empty($workshop) ? 'N/D' : $workshop->name;
    }
}
