<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestTemplate extends Model
{
    protected $table = 'tr07_test_templates';
    protected $primaryKey = 'tr07_template_id';

    const CREATED_AT = 'tr07_created_at';
    const UPDATED_AT = 'tr07_updated_at';

    protected $fillable = [
        'tr07_test_type',
        'tr07_template_name',
        'tr07_fields_config',
        'tr07_normal_ranges',
        'tr07_report_format',
        'tr07_is_active',
        'tr07_created_by'
    ];

    protected $casts = [
        'tr07_fields_config' => 'array',
        'tr07_normal_ranges' => 'array',
        'tr07_is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('tr07_is_active', 1);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'tr07_created_by');
    }
}
