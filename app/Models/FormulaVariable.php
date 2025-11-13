<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formulavariable extends Model
{
    protected $table = 'm24_formula_variables';
    protected $primaryKey = 'm24_formula_variable_id';
    protected $fillable = [
        'm23_formula_id',
        'm24_variable_key',
        'm24_label',
        'm24_unit',
        'm24_data_type',
        'm24_input_order',
        'm24_is_required',
        'created_at',
        'updated_at'
    ];

    public function formula()
    {
        return $this->belongsTo(Employee::class, 'm23_formula_id', 'm23_formula_id');
    }
}
