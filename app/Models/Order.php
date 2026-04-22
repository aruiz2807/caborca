<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Get the events associated with the order.
     */
    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->orderBy('created_at', 'desc');
    }
}
