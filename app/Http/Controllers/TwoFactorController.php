<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Way2SendSmsService;
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
        if (Session::has('admin_id') || Session::get('role_id') == -1) {
            return redirect()->route('admin.profile');
        }

        $user = User::with(['employee', 'ro'])->findOrFail(Session::get('tr01_user_id'));
        return view('profile.2fa.index', compact('user'));
    }

    /**
     * Initiate Google Authenticator setup.
     */
    public function setupGoogle()
    {
        $user = User::findOrFail(Session::get('tr01_user_id'));
        if ($user->tr01_two_factor_confirmed_at && $user->tr01_two_factor_method === 'google') {
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
        if ($user->tr01_two_factor_confirmed_at && $user->tr01_two_factor_method === 'email') {
            return redirect()->route('profile.2fa.index')->with('error', 'Email 2FA is already active.');
        }

        return view('profile.2fa.setup_email', compact('user'));
    }

    /**
     * Send email code for 2FA setup.
     */
    public function sendEmailCode(Request $request)
    {
        $user = User::findOrFail(Session::get('tr01_user_id'));
        $otp = rand(100000, 999999);

        // Cache OTP for 10 minutes
        Cache::put('2fa_setup_email_' . $user->tr01_user_id, $otp, now()->addMinutes(10));

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
     * Initiate Mobile SMS OTP setup.
     */
    public function setupMobile()
    {
        $user = User::with(['employee', 'ro'])->findOrFail(Session::get('tr01_user_id'));
        if ($user->tr01_two_factor_confirmed_at && $user->tr01_two_factor_method === 'mobile') {
            return redirect()->route('profile.2fa.index')->with('error', 'Mobile SMS 2FA is already active.');
        }

        return view('profile.2fa.setup_mobile', compact('user'));
    }

    /**
     * Send Mobile SMS OTP for 2FA setup.
     */
    public function sendMobileCode(Request $request)
    {
        $user = User::with(['employee', 'ro'])->findOrFail(Session::get('tr01_user_id'));

        $inputPhone = $request->input('phone') ?: $user->getPhoneNumber();

        $cleanMobile = preg_replace('/[^0-9]/', '', (string) $inputPhone);
        if (strlen($cleanMobile) === 12 && str_starts_with($cleanMobile, '91')) {
            $cleanMobile = substr($cleanMobile, 2);
        } elseif (strlen($cleanMobile) === 11 && str_starts_with($cleanMobile, '0')) {
            $cleanMobile = substr($cleanMobile, 1);
        }

        if (strlen($cleanMobile) !== 10) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid 10-digit mobile number.'
            ], 422);
        }

        $otp = rand(100000, 999999);

        // Cache OTP for 5 minutes (as per DLT template)
        Cache::put('2fa_setup_mobile_' . $user->tr01_user_id, [
            'otp'    => $otp,
            'mobile' => $cleanMobile
        ], now()->addMinutes(5));

        $smsService = app(Way2SendSmsService::class);
        $result = $smsService->sendOtp($cleanMobile, $otp);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS OTP: ' . ($result['message'] ?? 'Gateway error')
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully to mobile ending with ' . substr($cleanMobile, -4)
        ]);
    }

    /**
     * Confirm Mobile SMS OTP code and enable Mobile 2FA.
     */
    public function confirmMobile(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $user = User::with(['employee', 'ro'])->findOrFail(Session::get('tr01_user_id'));
        $cachedData = Cache::get('2fa_setup_mobile_' . $user->tr01_user_id);

        if (!$cachedData || $cachedData['otp'] != $request->code) {
            return redirect()->back()->with('error', 'Invalid or expired OTP code.');
        }

        // Save mobile number to employee/ro record if currently missing
        if (!empty($cachedData['mobile'])) {
            if ($user->employee && empty($user->employee->m06_phone)) {
                $user->employee->update(['m06_phone' => $cachedData['mobile']]);
            } elseif ($user->ro && empty($user->ro->m04_phone)) {
                $user->ro->update(['m04_phone' => $cachedData['mobile']]);
            }
        }

        $user->tr01_two_factor_method = 'mobile';
        $user->tr01_two_factor_secret = null;
        $user->tr01_two_factor_confirmed_at = now();
        $user->tr01_two_factor_recovery_codes = encrypt(json_encode($this->generateRecoveryCodes()));
        $user->save();

        Cache::forget('2fa_setup_mobile_' . $user->tr01_user_id);

        return redirect()->route('profile.2fa.index')->with('success', 'Mobile SMS 2FA enabled successfully! Please save your recovery codes.');
    }

    /**
     * Regenerate 2FA recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $user = User::findOrFail(Session::get('tr01_user_id'));
        if (!$user->tr01_two_factor_confirmed_at || !$user->tr01_two_factor_method) {
            return redirect()->route('profile.2fa.index')->with('error', 'Two-Factor Authentication is not enabled.');
        }

        $user->tr01_two_factor_recovery_codes = encrypt(json_encode($this->generateRecoveryCodes()));
        $user->save();

        return redirect()->route('profile.2fa.index')->with('success', '8 new emergency recovery codes have been generated successfully! Please save them safely.');
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

        $user = User::with(['employee', 'ro'])->findOrFail(Session::get('2fa_login_user_id'));

        // If it's email, automatically send code if not already sent
        if ($user->tr01_two_factor_method === 'email') {
            if (!Cache::has('2fa_login_email_' . $user->tr01_user_id)) {
                $otp = rand(100000, 999999);
                Cache::put('2fa_login_email_' . $user->tr01_user_id, $otp, now()->addMinutes(10));

                Mail::raw("Your 2FA Login Code is: {$otp}", function ($message) use ($user) {
                    $message->to($user->tr01_email)
                        ->subject('Your Two-Factor Authentication Code');
                });
            }
        } elseif ($user->tr01_two_factor_method === 'mobile') {
            // If it's mobile SMS, automatically send OTP if not already cached
            if (!Cache::has('2fa_login_mobile_' . $user->tr01_user_id)) {
                $phone = $user->getPhoneNumber();
                if ($phone) {
                    $otp = rand(100000, 999999);
                    Cache::put('2fa_login_mobile_' . $user->tr01_user_id, $otp, now()->addMinutes(5));

                    $smsService = app(Way2SendSmsService::class);
                    $smsService->sendOtp($phone, $otp);
                }
            }
        }

        return view('auth.2fa_challenge', compact('user'));
    }

    /**
     * Resend 2FA OTP for Email or Mobile SMS during login challenge.
     */
    public function resendLoginOtp(Request $request)
    {
        if (!Session::has('2fa_login_user_id')) {
            return response()->json(['success' => false, 'message' => 'Login session expired. Please start again.'], 401);
        }

        $user = User::with(['employee', 'ro'])->findOrFail(Session::get('2fa_login_user_id'));
        $otp = rand(100000, 999999);

        if ($user->tr01_two_factor_method === 'mobile') {
            $phone = $user->getPhoneNumber();
            if (!$phone) {
                return response()->json(['success' => false, 'message' => 'No mobile number found for your account.'], 400);
            }

            Cache::put('2fa_login_mobile_' . $user->tr01_user_id, $otp, now()->addMinutes(5));
            $smsService = app(Way2SendSmsService::class);
            $res = $smsService->sendOtp($phone, $otp);

            if ($res['success']) {
                return response()->json(['success' => true, 'message' => 'A new OTP has been sent via SMS.']);
            }
            return response()->json(['success' => false, 'message' => 'Failed to send SMS: ' . ($res['message'] ?? 'Gateway error')], 500);
        } elseif ($user->tr01_two_factor_method === 'email') {
            Cache::put('2fa_login_email_' . $user->tr01_user_id, $otp, now()->addMinutes(10));
            Mail::raw("Your 2FA Login Code is: {$otp}", function ($message) use ($user) {
                $message->to($user->tr01_email)
                    ->subject('Your Two-Factor Authentication Code');
            });
            return response()->json(['success' => true, 'message' => 'A new OTP has been sent to your email.']);
        }

        return response()->json(['success' => false, 'message' => 'Resend OTP is not supported for Authenticator App.'], 400);
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

        $user = User::with(['employee', 'ro'])->findOrFail(Session::get('2fa_login_user_id'));
        $isValid = false;

        // Check if user is using an emergency recovery code
        if ($user->tr01_two_factor_recovery_codes) {
            $recoveryCodes = json_decode(decrypt($user->tr01_two_factor_recovery_codes), true);
            if (is_array($recoveryCodes) && in_array($request->code, $recoveryCodes)) {
                $isValid = true;
                // Remove used backup code
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
            } elseif ($user->tr01_two_factor_method === 'mobile') {
                $cachedOtp = Cache::get('2fa_login_mobile_' . $user->tr01_user_id);
                if ($cachedOtp && $cachedOtp == $request->code) {
                    $isValid = true;
                    Cache::forget('2fa_login_mobile_' . $user->tr01_user_id);
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
