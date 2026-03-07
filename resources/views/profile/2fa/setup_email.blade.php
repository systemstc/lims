@extends('layouts.app_back')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Setup Email OTP</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Secure your account with Email One-Time Passwords.</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ route('profile.2fa.index') }}"
                                    class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                                    <em class="icon ni ni-arrow-left"></em><span>Back</span>
                                </a>
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
                                <div class="row no-gutters">
                                    <div class="col-md-6 pt-4 pb-4">
                                        <h5 class="title mb-3">Send Verification Code</h5>
                                        <p class="text-soft mb-4">Click the button below to send a 6-digit confirmation code
                                            to your registered email address: <strong>{{ $user->tr01_email }}</strong></p>

                                        <button class="btn btn-outline-primary" id="btn-send-code">
                                            <em class="icon ni ni-mail"></em>
                                            <span>Send Code to Email</span>
                                        </button>

                                        <div id="send-code-message" class="mt-3 text-success d-none">
                                            <em class="icon ni ni-check-circle"></em> Code sent successfully! It may take a
                                            minute or two to arrive.
                                        </div>
                                        <div id="send-code-error" class="mt-3 text-danger d-none">
                                            <em class="icon ni ni-cross-circle"></em> Failed to send code. Please try again.
                                        </div>

                                        <div class="alert alert-info mt-4">
                                            <em class="icon ni ni-info-fill"></em>
                                            <small>Every time you log in, we will send an email to this address containing
                                                your authentication code.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-md-5 pt-4 pb-4 border-left">
                                        <h5 class="title mb-3">Verify Code</h5>
                                        <p class="text-soft mb-4">Enter the 6-digit code received in your email to verify
                                            setup.</p>

                                        <form action="{{ route('profile.2fa.confirm_email') }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label class="form-label" for="code">Authentication Code</label>
                                                <div class="form-control-wrap">
                                                    <input type="text"
                                                        class="form-control form-control-lg text-center font-weight-bold fs-20px"
                                                        id="code" name="code" placeholder="000000" maxlength="6"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                                                </div>
                                                @error('code')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group mt-4">
                                                <button type="submit" class="btn btn-lg btn-primary btn-block">Verify &
                                                    Enable</button>
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
                '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Sending...';
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
                    btn.innerHTML = '<em class="icon ni ni-mail"></em> <span>Resend Code</span>';
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
                    btn.innerHTML = '<em class="icon ni ni-mail"></em> <span>Resend Code</span>';
                    btn.disabled = false;
                    msgError.classList.remove('d-none');
                });
        });
    </script>
@endsection
