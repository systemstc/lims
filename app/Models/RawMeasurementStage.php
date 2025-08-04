<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMeasurementStage extends Model
{
    protected $table = 'tr03_measurement_stages';
    protected $primaryKey = 'tr03_measurement_stage_id';
    protected $fillable = [
        'tr00_sample_id',
        'm12_test_id',
        'tr03_test_set_no',
        'm18_stage_id',
        'tr03_input',
        'tr03_output',
        'tr01_created_by',
        'tr03_status',
    ];
}
