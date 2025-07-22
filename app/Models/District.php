<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    protected $table = 'm02_districts';
    protected $primaryKey = 'm02_district_id';
    protected $fillable = [
        'm02_name',
        'm02_state_id',
        'm02_status'
    ];

    public function state():BelongsTo{
        return $this->belongsTo(State::class, 'm01_state_id');
    }
}
