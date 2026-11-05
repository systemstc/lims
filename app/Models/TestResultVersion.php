<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResultVersion extends Model
{
    use HasFactory;

    protected $table = 'tr09_test_reports';
    protected $primaryKey = 'tr09_test_report_id';

    protected $fillable = [
        'm04_ro_id',
        'tr04_reference_id',
        'tr09_version_number',
        'm06_generated_by',
        'tr09_generated_at',
        'tr09_report_data',
        'tr09_report_file_path',
        'tr09_status',
        'tr09_is_current',
    ];

    /**
     * Backward-compatibility accessor for tr07_version_number
     */
    public function getTr07VersionNumberAttribute()
    {
        return $this->tr09_version_number ?? 1;
    }
}

