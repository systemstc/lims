<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestTemplateParameter extends Model
{
    use HasFactory;

    protected $table = 'tr09_test_template_parameters';
    protected $primaryKey = 'tr09_parameter_id';

    protected $fillable = [
        'tr08_test_template_id',
        'tr09_name',
        'tr09_inputs',
        'tr09_min',
        'tr09_max',
        'tr09_formula',
    ];

    public function template()
    {
        return $this->belongsTo(TestTemplate::class, 'tr08_test_template_id', 'tr08_test_template_id');
    }
}

