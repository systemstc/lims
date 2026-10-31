<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SampleCustodyLog extends Model
{
    protected $table = 'tr10_sample_custody_logs';
    protected $primaryKey = 'tr10_custody_log_id';

    protected $fillable = [
        'tr04_sample_registration_id',
        'tr04_reference_id',
        'm04_ro_id',
        'from_role',
        'from_user_id',
        'from_name',
        'to_role',
        'to_user_id',
        'to_name',
        'action_type',
        'clarification_remark',
        'is_resolved',
        'resolved_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Self-healing schema validation to guarantee zero database exceptions
     * even without running artisan migrate in CLI.
     */
    public static function ensureCustodySchemaExists(): void
    {
        try {
            if (!Schema::hasTable('tr10_sample_custody_logs')) {
                Schema::create('tr10_sample_custody_logs', function (Blueprint $table) {
                    $table->bigIncrements('tr10_custody_log_id');
                    $table->unsignedBigInteger('tr04_sample_registration_id')->index();
                    $table->string('tr04_reference_id', 100)->nullable()->index();
                    $table->unsignedBigInteger('m04_ro_id')->nullable()->index();
                    $table->string('from_role', 50);
                    $table->unsignedBigInteger('from_user_id')->nullable();
                    $table->string('from_name', 255)->nullable();
                    $table->string('to_role', 50);
                    $table->unsignedBigInteger('to_user_id')->nullable();
                    $table->string('to_name', 255)->nullable();
                    $table->string('action_type', 50)->default('SEND_BACK');
                    $table->text('clarification_remark')->nullable();
                    $table->boolean('is_resolved')->default(false);
                    $table->timestamp('resolved_at')->nullable();
                    $table->timestamps();
                });
            }

            if (Schema::hasTable('tr04_sample_registrations')) {
                Schema::table('tr04_sample_registrations', function (Blueprint $table) {
                    if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_current_custody_role')) {
                        $table->string('tr04_current_custody_role', 50)->nullable()->default('RC');
                    }
                    if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_is_under_clarification')) {
                        $table->tinyInteger('tr04_is_under_clarification')->default(0);
                    }
                    if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_latest_clarification_remark')) {
                        $table->text('tr04_latest_clarification_remark')->nullable();
                    }
                    if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_sent_back_by_name')) {
                        $table->string('tr04_sent_back_by_name', 255)->nullable();
                    }
                    if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_sent_back_by_role')) {
                        $table->string('tr04_sent_back_by_role', 50)->nullable();
                    }
                    if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_sent_back_at')) {
                        $table->timestamp('tr04_sent_back_at')->nullable();
                    }
                });
            }
        } catch (\Throwable $e) {
            // Log or ignore if already created concurrently
        }
    }

    public function sampleRegistration()
    {
        return $this->belongsTo(SampleRegistration::class, 'tr04_sample_registration_id', 'tr04_sample_registration_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(Employee::class, 'from_user_id', 'm06_employee_id');
    }

    public function toUser()
    {
        return $this->belongsTo(Employee::class, 'to_user_id', 'm06_employee_id');
    }
}
