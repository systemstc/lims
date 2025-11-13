<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleAdditional extends Model
{
    protected $table = 'tr04_sample_additional_charges';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'sample_id',
        'item',
        'price',
        'full_amount'
    ];

    public function sample()
    {
        return $this->belongsTo(SampleRegistration::class, 'sample_id', 'tr04_sample_registration_id');
    }
}
