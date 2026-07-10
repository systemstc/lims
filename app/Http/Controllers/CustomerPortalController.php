<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SampleRegistration;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CustomerPortalController extends Controller
{
    public function showLogin()
    {
        return view('customer_portal.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'sample_reference_id' => 'required|string',
            'contact' => 'required|string'
        ]);

        $contact = $request->contact;
        $refId = $request->sample_reference_id;

        $sample = SampleRegistration::where('tr04_reference_id', $refId)->first();
        if (!$sample) {
            return back()->withErrors(['sample_reference_id' => 'Invalid Sample Reference ID'])->withInput();
        }

        $customer = Customer::find($sample->m07_customer_id);
        if (!$customer) {
            return back()->withErrors(['sample_reference_id' => 'Customer not found for this sample'])->withInput();
        }

        if ($customer->m07_email !== $contact && $customer->m07_phone !== $contact) {
            return back()->withErrors(['contact' => 'Email or Mobile does not match records'])->withInput();
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        Session::put('customer_otp', $otp);
        Session::put('customer_otp_id', $customer->m07_customer_id);

        // Normally you would send this OTP via Email or SMS here.
        // For demonstration, we log it.
        Log::info("Customer OTP for {$contact} is {$otp}");

        // Optionally, flash the OTP for testing purposes
        Session::flash('info', "OTP Sent! (For testing: $otp)");

        return redirect()->route('customer.otp');
    }

    public function showOtp()
    {
        if (!Session::has('customer_otp_id')) {
            return redirect()->route('customer.login');
        }
        return view('customer_portal.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        $sessionOtp = Session::get('customer_otp');
        if ($request->otp == $sessionOtp) {
            // Login success
            Session::put('customer_logged_in', true);
            Session::put('customer_id', Session::get('customer_otp_id'));
            
            Session::forget('customer_otp');
            Session::forget('customer_otp_id');

            return redirect()->route('customer.dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid OTP']);
    }

    public function logout()
    {
        Session::forget('customer_logged_in');
        Session::forget('customer_id');
        return redirect()->route('customer.login');
    }

    public function dashboard()
    {
        if (!Session::get('customer_logged_in')) return redirect()->route('customer.login');

        $customerId = Session::get('customer_id');
        $customer = Customer::find($customerId);
        $wallet = Wallet::where('m07_customer_id', $customerId)->first();
        $samples = SampleRegistration::where('m07_customer_id', $customerId)->orderBy('created_at', 'desc')->get();

        return view('customer_portal.dashboard', compact('customer', 'wallet', 'samples'));
    }

    public function profile()
    {
        if (!Session::get('customer_logged_in')) return redirect()->route('customer.login');

        $customer = Customer::find(Session::get('customer_id'));
        return view('customer_portal.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        if (!Session::get('customer_logged_in')) return redirect()->route('customer.login');

        $customer = Customer::find(Session::get('customer_id'));
        $request->validate([
            'm07_name' => 'required|string|max:255',
            'm07_address' => 'required|string',
        ]);

        $customer->update([
            'm07_name' => $request->m07_name,
            'm07_address' => $request->m07_address
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }
}
