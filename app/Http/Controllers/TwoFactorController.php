<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA status and available methods.
     */
    public function index()
    {
        $user = User::findOrFail(Session::get('tr01_user_id'));
        return view('profile.2fa.index', compact('user'));
    }

    /**
     * Initiate Google Authenticator setup.
     */
    public function setupGoogle()
    {
        $user = User::findOrFail(Session::get('tr01_user_id'));
        if ($user->two_factor_confirmed_at && $user->tr01_two_factor_method === 'google') {
            return redirect()->route('profile.2fa.index')->with('error', 'Google 2FA is already active.');
        }

        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();

        // Temporarily store secret in session
        Session::put('2fa_setup_secret', $secret);

        $google2faUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->tr01_email,
            $secret
        );

        $qrCodeUrl = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)
            ->margin(1)
            ->generate($google2faUrl);

        return view('profile.2fa.setup_google', compact('qrCodeUrl', 'secret'));
    }

    /**
     * Confirm Google Authenticator code and save.
     */
    public function confirmGoogle(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $secret = Session::get('2fa_setup_secret');
        if (!$secret) {
            return redirect()->route('profile.2fa.setup_google')->with('error', 'Session expired. Please try again.');
        }

        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            $user = User::findOrFail(Session::get('tr01_user_id'));
            $user->tr01_two_factor_method = 'google';
            $user->tr01_two_factor_secret = encrypt($secret);
            $user->tr01_two_factor_confirmed_at = now();
            $user->tr01_two_factor_recovery_codes = encrypt(json_encode($this->generateRecoveryCodes()));
            $user->save();

            Session::forget('2fa_setup_secret');

            return redirect()->route('profile.2fa.index')->with('success', 'Google Authenticator enabled successfully! Please save your recovery codes.');
        }

        return redirect()->back()->with('error', 'Invalid authentication code.');
    }

    /**
     * Initiate Email OTP setup.
     */
    public function setupEmail()
    {
        $user = User::findOrFail(Session::get('tr01_user_id'));
        if ($user->two_factor_confirmed_at && $user->tr01_two_factor_method === 'email') {
            return redirect()->route('profile.2fa.index')->with('error', 'Email 2FA is already active.');
        }

        return view('profile.2fa.setup_email', compact('user'));
    }

    /**
     * Send mapping to send email code for setup.
     */
    public function sendEmailCode(Request $request)
    {
        $user = User::findOrFail(Session::get('tr01_user_id'));
        $otp = rand(100000, 999999);

        // Cache OTP for 10 minutes
        Cache::put('2fa_setup_email_' . $user->tr01_user_id, $otp, now()->addMinutes(10));

        // Send Email (You can replace this with a proper Mail class)
        Mail::raw("Your 2FA Setup Code is: {$otp}", function ($message) use ($user) {
            $message->to($user->tr01_email)
                ->subject('Two-Factor Authentication Setup Code');
        });

        return response()->json(['success' => true, 'message' => 'OTP sent to your email.']);
    }

    /**
     * Confirm Email OTP code and save.
     */
    public function confirmEmail(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $user = User::findOrFail(Session::get('tr01_user_id'));
        $cachedOtp = Cache::get('2fa_setup_email_' . $user->tr01_user_id);

        if (!$cachedOtp || $cachedOtp != $request->code) {
            return redirect()->back()->with('error', 'Invalid or expired OTP code.');
        }

        $user->tr01_two_factor_method = 'email';
        $user->tr01_two_factor_secret = null; // No secret for email
        $user->tr01_two_factor_confirmed_at = now();
        $user->tr01_two_factor_recovery_codes = encrypt(json_encode($this->generateRecoveryCodes()));
        $user->save();

        Cache::forget('2fa_setup_email_' . $user->tr01_user_id);

        return redirect()->route('profile.2fa.index')->with('success', 'Email 2FA enabled successfully! Please save your recovery codes.');
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request)
    {
        $user = User::findOrFail(Session::get('tr01_user_id'));
        $user->tr01_two_factor_method = null;
        $user->tr01_two_factor_secret = null;
        $user->tr01_two_factor_confirmed_at = null;
        $user->tr01_two_factor_recovery_codes = null;
        $user->save();

        return redirect()->route('profile.2fa.index')->with('success', 'Two-Factor Authentication has been disabled.');
    }

    /**
     * Generate 8 random recovery codes.
     */
    private function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10) . '-' . Str::random(10);
        }
        return $codes;
    }

    /**
     * Show the 2FA challenge page.
     */
    public function showChallenge()
    {
        if (!Session::has('2fa_login_user_id')) {
            return redirect()->route('user_login');
        }

        $user = User::findOrFail(Session::get('2fa_login_user_id'));

        // If it's email, send the code automatically when landing on this page
        if ($user->tr01_two_factor_method === 'email') {
            // Check if there's already an active code to avoid spamming
            if (!Cache::has('2fa_login_email_' . $user->tr01_user_id)) {
                $otp = rand(100000, 999999);
                Cache::put('2fa_login_email_' . $user->tr01_user_id, $otp, now()->addMinutes(10));

                Mail::raw("Your 2FA Login Code is: {$otp}", function ($message) use ($user) {
                    $message->to($user->tr01_email)
                        ->subject('Your Two-Factor Authentication Code');
                });
            }
        }

        return view('auth.2fa_challenge', compact('user'));
    }

    /**
     * Verify the 2FA challenge and finalize login.
     */
    public function verifyChallenge(Request $request)
    {
        $request->validate(['code' => 'required']);

        if (!Session::has('2fa_login_user_id')) {
            return redirect()->route('user_login')->with('error', 'Login session expired. Please start again.');
        }

        $user = User::findOrFail(Session::get('2fa_login_user_id'));
        $isValid = false;

        // Check if user is using a recovery code
        if ($user->tr01_two_factor_recovery_codes) {
            $recoveryCodes = json_decode(decrypt($user->tr01_two_factor_recovery_codes), true);
            if (is_array($recoveryCodes) && in_array($request->code, $recoveryCodes)) {
                $isValid = true;
                // Remove used back up code
                $recoveryCodes = array_filter($recoveryCodes, fn($c) => $c !== $request->code);
                $user->tr01_two_factor_recovery_codes = encrypt(json_encode(array_values($recoveryCodes)));
                $user->save();
            }
        }

        if (!$isValid) {
            if ($user->tr01_two_factor_method === 'google') {
                $google2fa = app('pragmarx.google2fa');
                $isValid = $google2fa->verifyKey(decrypt($user->tr01_two_factor_secret), $request->code);
            } elseif ($user->tr01_two_factor_method === 'email') {
                $cachedOtp = Cache::get('2fa_login_email_' . $user->tr01_user_id);
                if ($cachedOtp && $cachedOtp == $request->code) {
                    $isValid = true;
                    Cache::forget('2fa_login_email_' . $user->tr01_user_id);
                }
            }
        }

        if ($isValid) {
            // Call AuthController to complete the login
            return app(AuthController::class)->completeLogin($user, $request);
        }

        return redirect()->back()->with('error', 'Invalid authentication code.');
    }
}
