<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('parts_available')->default(false)->after('status');
            $table->date('parts_arrival_date')->nullable()->after('parts_available');
        });

        // Update existing orders to shift statuses, making room for PARTS (2) and PARTS_AVAILABLE (3)
        DB::statement('UPDATE orders SET status = 7 WHERE status = 5'); // NO_SHOW (5 -> 7)
        DB::statement('UPDATE orders SET status = 6 WHERE status = 4'); // FINISHED (4 -> 6)
        DB::statement('UPDATE orders SET status = 5 WHERE status = 3'); // ENTERED (3 -> 5)
        DB::statement('UPDATE orders SET status = 4 WHERE status = 2'); // SCHEDULED (2 -> 4)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert statuses
        DB::statement('UPDATE orders SET status = 2 WHERE status = 4');
        DB::statement('UPDATE orders SET status = 3 WHERE status = 5');
        DB::statement('UPDATE orders SET status = 4 WHERE status = 6');
        DB::statement('UPDATE orders SET status = 5 WHERE status = 7');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('parts_available');
            $table->dropColumn('parts_arrival_date');
        });
    }
};
