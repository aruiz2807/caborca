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
            $table->foreignId('appointment_workshop_id')->constrained(
                table: 'workshops'
            )->after('appointment')->nullable();
            $table->date('appointment_date')->after('appointment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('appointment_workshop_id');
            $table->dropColumn('appointment_date');
        });
    }
};
