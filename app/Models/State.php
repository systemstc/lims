<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'm01_states';
    protected $primaryKey = 'm01_state_id';
    protected $fillable = [
        'm01_name',
        'm01_status'
    ];
}
