<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_order');
            $table->string('economic_number');
            $table->string('order_file');

            $table->foreignId('vehicle_dependency_id')->constrained(
                table: 'dependencies'
            );
            $table->string('vehicle_vin', 17);
            $table->string('vehicle_description');
            $table->string('vehicle_plate', 10);

            $table->foreignId('service_type_id')->constrained(
                table: 'service_types'
            );
            $table->date('service_requested_date');
            $table->foreignId('service_location_id')->constrained(
                table: 'locations'
            );
            $table->text('service_description');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
