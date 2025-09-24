<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    protected $table = 'tr11_supports';
    protected $primaryKey = 'tr11_support_id';
    protected $fillable = [
        'tr11_first_name',
        'tr11_last_name',
        'tr11_email',
        'tr11_phone',
        'tr11_organization',
        'tr11_laboratory',
        'tr11_message',
    ];
}
