<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\XmlConfiguration\RemoveRegisterMockObjectsFromTestArgumentsRecursivelyAttribute;

class SampleController extends Controller
{
    public function searchNames(Request $request)
    {
        $searchTerm = $request->input('query');
        try {
            $results = DB::select('CALL find_similar_names(?)', [$searchTerm . '%']);
            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Error calling stored procedure find_similar_names: ' . $e->getMessage(), ['query' => $searchTerm]);
            return response()->json(['error' => 'Could not retrieve search results.'], 500);
        }
    }

    public function registerSample()
    {
        $customers = Customer::all();
        return view('samples.sample_regsitration', compact('customers'));
    }
}
