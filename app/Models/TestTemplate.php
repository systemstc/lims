<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestTemplate extends Model
{
    use HasFactory;

    protected $table = 'tr08_test_templates';
    protected $primaryKey = 'tr08_test_template_id';

    protected $fillable = [
        'm12_test_id',
        'tr08_test_type',
        'tr08_times_test_perform',
        'tr08_formula',
        'tr08_fields_config',
        'tr08_status',
        'm06_created_by',
    ];

    protected $casts = [
        'tr08_fields_config' => 'array',
        'tr08_times_test_perform' => 'integer',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_id', 'm12_test_id');
    }

    public function parameters()
    {
        return $this->hasMany(TestTemplateParameter::class, 'tr08_test_template_id', 'tr08_test_template_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'm06_created_by', 'm06_employee_id');
    }
}

