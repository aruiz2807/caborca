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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('service_order_workshop_id')->constrained(
                table: 'workshops'
            )->after('appointment_workshop_id')->nullable();
            $table->string('service_order_user', 5)->after('appointment_workshop_id')->nullable();
            $table->string('service_order_mileage')->after('appointment_workshop_id')->nullable();
            $table->string('service_order_cone', 15)->after('appointment_workshop_id')->nullable();
            $table->string('service_order_status', 25)->after('appointment_workshop_id')->nullable();
            $table->date('service_order_date')->after('appointment_workshop_id')->nullable();
            $table->string('service_order')->after('appointment_workshop_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('service_order');
            $table->dropColumn('service_order_date');
            $table->dropColumn('service_order_status');
            $table->dropColumn('service_order_cone');
            $table->dropColumn('service_order_mileage');
            $table->dropColumn('service_order_user');
            $table->dropColumn('service_order_workshop_id');
        });
    }
};
