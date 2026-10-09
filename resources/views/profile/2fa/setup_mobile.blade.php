@extends('layouts.app_back')

@section('content')
    <div class="nk-content-xxl">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Setup Mobile SMS One-Time Passcode (OTP)</h3>
                                <div class="nk-block-des text-soft fs-6">
                                    <p>Protect your account with instant 6-digit SMS verification codes sent via the Way2Send Gateway.</p>
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

                    @if (session('error'))
                        <div class="alert alert-danger alert-icon mb-4">
                            <em class="icon ni ni-cross-circle"></em>
                            <strong>{{ session('error') }}</strong>
                        </div>
                    @endif

                    <div class="nk-block">
                        <div class="card card-bordered" style="border-radius: 16px; box-shadow: 0 4px 14px rgba(15,23,42,0.03);">
                            <div class="card-inner p-4">
                                <div class="row g-4 align-items-center">
                                    <div class="col-md-6 border-end-md pb-4 pb-md-0">
                                        <div class="badge bg-success badge-pill mb-3 fs-6">Step 1: Request SMS Code</div>
                                        <h5 class="title mb-2">Send Passcode to Mobile</h5>
                                        <p class="text-soft mb-3 fs-6">Confirm your 10-digit mobile number below to receive an authentication code.</p>

                                        <div class="form-group mb-3">
                                            <label class="form-label font-weight-bold" for="phone_number">Mobile Number</label>
                                            <div class="form-control-wrap">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text font-weight-bold">+91</span>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control form-control-lg font-weight-bold"
                                                           id="phone_number"
                                                           name="phone_number"
                                                           placeholder="10-digit mobile number"
                                                           maxlength="10"
                                                           value="{{ $user->getPhoneNumber() }}"
                                                           oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                </div>
                                            </div>
                                            <small class="text-muted">Must be an active 10-digit Indian mobile number capable of receiving SMS.</small>
                                        </div>

                                        <button class="btn btn-primary btn-lg" id="btn-send-code">
                                            <em class="icon ni ni-send"></em>
                                            <span>Send Code via SMS</span>
                                        </button>

                                        <div id="send-code-message" class="mt-3 text-success d-none font-weight-bold">
                                            <em class="icon ni ni-check-circle"></em> <span id="success-text">Code sent successfully! Check your phone inbox.</span>
                                        </div>
                                        <div id="send-code-error" class="mt-3 text-danger d-none font-weight-bold">
                                            <em class="icon ni ni-cross-circle"></em> <span id="error-text">Failed to send code. Please try again.</span>
                                        </div>

                                        <div class="alert alert-info alert-icon mt-4 mb-0">
                                            <em class="icon ni ni-info-fill"></em>
                                            <small class="fs-6">Codes are valid for <strong>5 minutes</strong>. Message content is delivered via  header <strong>TEXCOM</strong>.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 ps-md-5">
                                        <div class="badge badge-pill bg-success mb-3 fs-6">Step 2: Confirm Code</div>
                                        <h5 class="title mb-2">Verify Received SMS Code</h5>
                                        <p class="text-soft mb-4 fs-6">Enter the 6-digit OTP received on your mobile device to activate Mobile 2FA.</p>

                                        <form action="{{ route('profile.2fa.confirm_mobile') }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label class="form-label font-weight-bold fs-6" for="code">6-Digit SMS Code</label>
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
                                                    <span>Verify &amp; Enable Mobile 2FA</span>
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
            const phoneInput = document.getElementById('phone_number');
            const msgSuccess = document.getElementById('send-code-message');
            const msgError = document.getElementById('send-code-error');
            const successText = document.getElementById('success-text');
            const errorText = document.getElementById('error-text');

            const phone = phoneInput ? phoneInput.value.trim() : '';
            if (phone.length !== 10) {
                alert('Please enter a valid 10-digit mobile number before requesting the code.');
                phoneInput.focus();
                return;
            }

            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending SMS...';
            msgSuccess.classList.add('d-none');
            msgError.classList.add('d-none');

            fetch('{{ route('profile.2fa.send_mobile_code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ phone: phone })
                })
                .then(response => response.json())
                .then(data => {
                    btn.innerHTML = '<em class="icon ni ni-reload"></em> <span>Resend SMS Code</span>';
                    btn.disabled = false;

                    if (data.success) {
                        successText.innerText = data.message || 'OTP sent successfully! Check your phone messages.';
                        msgSuccess.classList.remove('d-none');
                        document.getElementById('code').focus();
                    } else {
                        errorText.innerText = data.message || 'Failed to send SMS code. Please try again.';
                        msgError.classList.remove('d-none');
                    }
                })
                .catch(err => {
                    btn.innerHTML = '<em class="icon ni ni-reload"></em> <span>Resend SMS Code</span>';
                    btn.disabled = false;
                    errorText.innerText = 'Network or server error while sending SMS. Please try again.';
                    msgError.classList.remove('d-none');
                });
        });
    </script>
@endsection
