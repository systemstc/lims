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
        $query = $modelClass::where($field, $value);

        // Filter by RO ID for Customers
        if ($model === 'Customer' && $request->has('m04_ro_id')) {
            $query->where('m04_ro_id', $request->m04_ro_id);
        }

        $exists = $query->exists();
        return response()->json([
            'exists' => $exists
        ]);
    }
}
