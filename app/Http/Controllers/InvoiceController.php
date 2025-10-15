<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SampleRegistration;


class InvoiceController extends Controller
{

    public function showInvoiceDetails($id)
   {
    $sample = SampleRegistration::with([
            'labSample',
            'package',
            'sampleTests.test',
            'sampleTests.standard.accreditationForCurrentRo',
            'customerType',
            'ro'
        ])->where('tr04_sample_registration_id', $id)->first();

        $sample->each(function ($sample) {
            $sample->sampleTests->each(function ($test) {
                $test->append(['primary_tests', 'secondary_tests']);
            });
        });

    return view('invoice.invoice_detail',compact('sample'));
   }


   public function showAllInvoiceDetails($id, Request $request)
   {

    $locationId = $request->query('location_id');
    $paymentBy = $request->query('payment_by');

    // dd($locationId, $paymentBy);

    $samples = SampleRegistration::with([
        'LabSample',
        'sampleTests.test',
        'customer'
    ])
    ->where('m07_customer_id',$id)
    ->where('m08_customer_location_id',$locationId)
    ->where('tr04_payment_by',$paymentBy)

    ->get();

    // dd($samples);

    $totalAmount = $samples->sum('tr04_total_charges');

    return view('invoice.invoice_details',compact('samples','totalAmount'));
   }

   public function generateCombinedInvoice(Request $request)
    {
        $referenceIds = $request->input('reference_ids', []);

        if (empty($referenceIds)) {
            return redirect()->back()->with('error', 'No samples selected.');
        }

        $samples = SampleRegistration::with(['labSample', 'sampleTests.test', 'customer'])
            ->whereIn('tr04_reference_id', $referenceIds)
            ->get();

        $totalAmount = $samples->sum('tr04_total_charges');

        return view('invoice.combined_invoice', compact('samples', 'totalAmount'));
    }




}
