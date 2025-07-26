<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function viewEmployee(){
        $employees = Employee::get();
        return view('employees.employees', compact('employees'));
    }
}
