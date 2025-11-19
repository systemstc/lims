<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\PasswordResetMail;

class PasswordResetController extends Controller
{
    // Show forgot password form
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    // Send password reset link
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:tr01_users,tr01_email'
        ]);

        // Generate token
        $token = Str::random(64);

        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Create new token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // Send email
        Mail::to($request->email)->send(new PasswordResetMail($token, $request->email));

        return back()->with('success', 'Password reset link has been sent to your email!');
    }

    // Show reset password form
    public function showResetForm($token)
    {
        // Get the email associated with this token
        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        $email = $tokenData ? $tokenData->email : old('email');

        return view('auth.reset_password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    // Reset password
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:tr01_users,tr01_email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        // Verify token
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return back()->withErrors(['email' => 'Invalid token!']);
        }

        // Check if token is expired (1 hour)
        $tokenCreated = Carbon::parse($tokenData->created_at);
        if ($tokenCreated->diffInHours(Carbon::now()) > 1) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Token has expired!']);
        }

        // Update user password
        User::where('tr01_email', $request->email)
            ->update(['tr01_password' => Hash::make($request->password)]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('user_login')->with('success', 'Password has been reset successfully!');
    }
}
