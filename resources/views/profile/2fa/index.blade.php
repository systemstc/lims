@extends('layouts.app_back')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Two-Factor Authentication</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Manage your account security settings.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <h5 class="card-title">Two-Factor Authentication (2FA) Status</h5>

                                @if ($user->two_factor_confirmed_at && $user->tr01_two_factor_method)
                                    <div class="alert alert-success mt-3">
                                        <em class="icon ni ni-check-circle"></em>
                                        <strong>2FA is currently ENABLED</strong> using
                                        <span
                                            class="text-uppercase font-weight-bold">{{ $user->tr01_two_factor_method }}</span>.
                                    </div>
                                    <p class="mt-2 text-muted">Your account is secured. When logging in, you will be
                                        required to enter a secure code.</p>

                                    <form action="{{ route('profile.2fa.disable') }}" method="POST" class="mt-4"
                                        onsubmit="return confirm('Are you sure you want to disable Two-Factor Authentication? Your account will be less secure.');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Disable 2FA</button>
                                    </form>

                                    @if ($user->tr01_two_factor_recovery_codes)
                                        <div class="mt-5">
                                            <h6 class="title">Recovery Codes</h6>
                                            <p class="text-soft">If you lose access to your device, you can use one of these
                                                recovery codes to log in. Each code can only be used once. Please save them
                                                in a secure password manager.</p>
                                            <div class="bg-light p-3 rounded"
                                                style="font-family: monospace; font-size: 1.1em; letter-spacing: 2px;">
                                                @php
                                                    $codes =
                                                        json_decode(
                                                            decrypt($user->tr01_two_factor_recovery_codes),
                                                            true,
                                                        ) ?? [];
                                                @endphp
                                                <div class="row">
                                                    @foreach ($codes as $code)
                                                        <div class="col-sm-6 mb-2">{{ $code }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-warning mt-3">
                                        <em class="icon ni ni-alert-circle"></em>
                                        <strong>2FA is currently DISABLED</strong>.
                                    </div>
                                    <p class="mt-2 text-muted">Add additional security to your account using two-factor
                                        authentication.</p>

                                    <div class="row g-4 mt-2">
                                        <div class="col-sm-6">
                                            <div class="card card-bordered h-100">
                                                <div class="card-inner">
                                                    <h5 class="card-title">Authenticator App</h5>
                                                    <p class="card-text">Use an app like Google Authenticator or Authy to
                                                        generate secure codes. Completely offline and highly secure.</p>
                                                    <a href="{{ route('profile.2fa.setup_google') }}"
                                                        class="btn btn-primary">Setup Authenticator App</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="card card-bordered h-100">
                                                <div class="card-inner">
                                                    <h5 class="card-title">Email OTP</h5>
                                                    <p class="card-text">Receive a 6-digit code via email every time you log
                                                        in. Simple and requires no additional app.</p>
                                                    <a href="{{ route('profile.2fa.setup_email') }}"
                                                        class="btn btn-outline-primary">Setup Email OTP</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
