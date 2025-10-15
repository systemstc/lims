<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultVersion extends Model
{
    protected $table = 'tr09_report_versions';
    protected $primaryKey = 'tr09_report_version_id';
    protected $fillable = [
        'tr09_version_number',
        'tr09_findings',
        'tr09_result_values',
        'tr09_report_template',
        'tr09_change_reason',
        'm06_changed_by',
        'tr09_changed_at',
        'tr09_is_current',
    ];
}
