<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'vehicle_brand_id',
        'service_type_id',
        'service_requested_date',
        'service_location_id',
        'service_description',
        'appointment',
        'appointment_date',
        'appointment_workshop_id',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }

    /**
     * The attributes that are appended when model is retrieved
     *
     * @var array<int, string>
     */
    protected $appends = [
        'vehicle_dependency_label',
        'service_type_label',
        'service_location_label',
        'appointment_workshop_label',
    ];

    /**
     * Get the dependency that owns the order.
     */
    public function dependency(): BelongsTo
    {
        return $this->belongsTo(Dependency::class, 'vehicle_dependency_id');
    }

    /**
     * Get the service type that owns the order.
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_type_id');
    }

    /**
     * Get the location where the service is requested.
     */
    public function serviceLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'service_location_id');
    }

    /**
     * Get the workshop that owns the order.
     */
    public function appointmentWorkshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class, 'appointment_workshop_id');
    }

    /**
     * Get the order vehicle's dependency name
     */
    protected function getVehicleDependencyLabelAttribute()
    {
        return $this->dependency?->name ?? 'N/D';
    }

    /**
     * Get the order's service type name
     */
    protected function getServiceTypeLabelAttribute()
    {
        return $this->serviceType?->name ?? 'N/D';
    }

    /**
     * Get the order's location name
     */
    protected function getServiceLocationLabelAttribute()
    {
        return $this->serviceLocation?->name ?? 'N/D';
    }

    /**
     * Get the order appointment's workshop name
     */
    protected function getAppointmentWorkshopLabelAttribute()
    {
        return $this->appointmentWorkshop?->name ?? 'N/D';
    }
}
