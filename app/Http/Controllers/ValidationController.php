<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Test;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    public function checkField(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $model = $request->model;
        $allowedModels = [
            'Customer' => Customer::class,
            'Test' => Test::class,
        ];
        if (!array_key_exists($model, $allowedModels)) {
            return response()->json(['exists' => false, 'error' => 'Invalid model']);
        }
        $modelClass = $allowedModels[$model];
        $exists = $modelClass::where($field, $value)->exists();
        return response()->json([
            'exists' => $exists
        ]);
    }
}
