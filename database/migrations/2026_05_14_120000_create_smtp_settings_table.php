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
        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('smtp');
            $table->string('host')->nullable();
            $table->unsignedInteger('port')->default(587);
            $table->string('encryption')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->string('oauth_tenant_id')->nullable();
            $table->string('oauth_client_id')->nullable();
            $table->text('oauth_client_secret')->nullable();
            $table->string('oauth_mailbox')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smtp_settings');
    }
};
