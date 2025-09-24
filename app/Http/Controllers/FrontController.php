<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function contactSupport(Request $request)
    {
        if (!$request->isMethod('POST')) {
            abort(405, 'Method Not Allowed');
        }
        $validator = Validator::make($request->all(), [
            "txt_first_name"   => "required|regex:/^[a-zA-Z\s]+$/|max:255",
            "txt_last_name"    => "nullable|regex:/^[a-zA-Z\s]+$/|max:255",
            "txt_email"        => "required|email:rfc|max:255",
            "txt_phone"        => "required|digits_between:7,15",
            "txt_organization" => "nullable|string|max:255",
            "txt_lab_type"     => "required|string|max:255",
            "txt_message"      => "required|string|max:1000",
        ], [
            'txt_first_name.required' => 'First name is required',
            'txt_first_name.regex' => 'First name should only contain alphabets',
            'txt_first_name.max' => 'First name cannot exceed 255 characters',

            'txt_last_name.regex' => 'Last name should only contain alphabets',
            'txt_last_name.max' => 'Last name cannot exceed 255 characters',

            'txt_email.required' => 'Email is required',
            'txt_email.email' => 'Please enter a valid email address',

            'txt_phone.required' => 'Phone number is required',
            'txt_phone.digits_between' => 'Phone number must be between 7 to 15 digits',

            'txt_organization.string' => 'Organization name must be a string',
            'txt_organization.max' => 'Organization name cannot exceed 255 characters',

            'txt_lab_type.required' => 'Lab type is required',
            'txt_lab_type.string' => 'Lab type must be a string',

            'txt_message.required' => 'Message is required',
            'txt_message.string' => 'Message must be a valid string',
            'txt_message.max' => 'Message cannot exceed 1000 characters',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'tr11_first_name'   => trim($request->txt_first_name),
            'tr11_last_name'    => trim($request->txt_last_name),
            'tr11_email'        => strtolower(trim($request->txt_email)),
            'tr11_phone'        => preg_replace('/\D/', '', $request->txt_phone),
            'tr11_organization' => trim($request->txt_organization),
            'tr11_laboratory'   => trim($request->txt_lab_type),
            'tr11_message'      => trim($request->txt_message),
        ];

        try {
            $support = Support::create($data);
            if ($support) {
                Session::flash('type', 'success');
                Session::flash('message', 'Thank you for contacting us, we will get back to you soon!');
            } else {
                Session::flash('type', 'error');
                Session::flash('message', 'Something went wrong. Please try again later.');
            }
        } catch (\Exception $e) {
            Log::error('Support Form Error: ' . $e->getMessage(), ['data' => $data]);
            Session::flash('type', 'error');
            Session::flash('message', 'An unexpected error occurred. Please try again later.');
        }
        return redirect()->back();
    }
}
