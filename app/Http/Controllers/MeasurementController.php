<?php

namespace App\Http\Controllers;

use App\Models\RawMeasurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function viewMeasurements()
    {
        $measurements = RawMeasurement::with('test', 'user')->get();
        return view('measurement.simple.measurements', compact('measurements'));
    }
}
