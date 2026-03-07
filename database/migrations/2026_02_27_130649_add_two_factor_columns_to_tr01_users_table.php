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
        Schema::table('tr01_users', function (Blueprint $table) {
            $table->string('tr01_two_factor_method')->nullable()->comment('google, email, mobile');
            $table->text('tr01_two_factor_secret')->nullable();
            $table->text('tr01_two_factor_recovery_codes')->nullable();
            $table->timestamp('tr01_two_factor_confirmed_at')->nullable();
            $table->boolean('tr01_is_2fa_blocked')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr01_users', function (Blueprint $table) {
            $table->dropColumn([
                'tr01_two_factor_method',
                'tr01_two_factor_secret',
                'tr01_two_factor_recovery_codes',
                'tr01_two_factor_confirmed_at',
                'tr01_is_2fa_blocked'
            ]);
        });
    }
};
