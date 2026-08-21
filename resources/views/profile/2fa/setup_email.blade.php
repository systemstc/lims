@extends('layouts.app_back')

@section('content')
    <div class="nk-content-xxl">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Setup Email One-Time Passcode (OTP)</h3>
                                <div class="nk-block-des text-soft fs-6">
                                    <p>Secure your account with 6-digit one-time passcodes sent to your email.</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ route('profile.2fa.index') }}"
                                    class="btn btn-outline-primary d-none d-sm-inline-flex fs-6">
                                    <em class="icon ni ni-arrow-left"></em><span>Back</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="row g-4 align-items-center">
                                    <div class="col-md-6 border-end-md pb-4 pb-md-0">
                                        <div class="badge bg-info badge-pill mb-3 fs-6">Step 1: Request Code</div>
                                        <h5 class="title mb-2">Send Passcode to Email</h5>
                                        <p class="text-soft mb-4 fs-6">Click the button below to receive a 6-digit confirmation code at your registered email address: <strong>{{ $user->tr01_email }}</strong></p>

                                        <button class="btn btn-primary btn-lg" id="btn-send-code">
                                            <em class="icon ni ni-mail"></em>
                                            <span>Send Code to Email</span>
                                        </button>

                                        <div id="send-code-message" class="mt-3 text-success d-none font-weight-bold">
                                            <em class="icon ni ni-check-circle"></em> Code sent successfully! Check your inbox or spam folder.
                                        </div>
                                        <div id="send-code-error" class="mt-3 text-danger d-none font-weight-bold">
                                            <em class="icon ni ni-cross-circle"></em> Failed to send code. Please try again.
                                        </div>

                                        <div class="alert alert-info alert-icon mt-4 mb-0">
                                            <em class="icon ni ni-info-fill"></em>
                                            <small class="fs-6">Whenever you log in, we will automatically send an authentication code to your email.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 ps-md-5">
                                        <div class="badge badge-pill bg-success mb-3 fs-6">Step 2: Confirm Code</div>
                                        <h5 class="title mb-2">Verify Received Code</h5>
                                        <p class="text-soft mb-4 fs-6">Enter the 6-digit passcode received in your email to enable Email 2FA.</p>

                                        <form action="{{ route('profile.2fa.confirm_email') }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label class="form-label font-weight-bold fs-6" for="code">6-Digit Email Code</label>
                                                <div class="form-control-wrap">
                                                    <input type="text"
                                                        class="form-control form-control-lg text-center font-weight-bold fs-22px font-monospace"
                                                        id="code" name="code" placeholder="000000" maxlength="6"
                                                        style="letter-spacing: 4px;"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                                                </div>
                                                @error('code')
                                                    <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group mt-4">
                                                <button type="submit" class="btn btn-lg btn-success btn-block fs-6">
                                                    <em class="icon ni ni-shield-check"></em>
                                                    <span>Verify &amp; Enable Email 2FA</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btn-send-code').addEventListener('click', function() {
            const btn = this;
            const msgSuccess = document.getElementById('send-code-message');
            const msgError = document.getElementById('send-code-error');

            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending...';
            msgSuccess.classList.add('d-none');
            msgError.classList.add('d-none');

            fetch('{{ route('profile.2fa.send_email_code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    btn.innerHTML = '<em class="icon ni ni-reload"></em> <span>Resend Code</span>';
                    btn.disabled = false;

                    if (data.success) {
                        msgSuccess.classList.remove('d-none');
                        document.getElementById('code').focus();
                    } else {
                        msgError.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btn.innerHTML = '<em class="icon ni ni-reload"></em> <span>Resend Code</span>';
                    btn.disabled = false;
                    msgError.classList.remove('d-none');
                });
        });
    </script>
@endsection
