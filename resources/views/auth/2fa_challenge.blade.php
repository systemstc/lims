<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="LIMS">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ asset('backAssets/images/logo.jpeg') }}">
    <!-- Page Title  -->
    <title>Two-Factor Authentication | LIMS</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css?ver=3.2.0') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('backAssets/css/theme.css?ver=3.2.0') }}">
</head>

<body class="nk-body bg-white npc-general pg-auth">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <a href="{{ url('/') }}" class="logo-link">
                                <img class="logo-light logo-img logo-img-lg"
                                    src="{{ asset('backAssets/images/logo.png') }}" alt="logo">
                                <img class="logo-dark logo-img logo-img-lg"
                                    src="{{ asset('backAssets/images/logo.png') }}" alt="logo-dark">
                            </a>
                        </div>
                        <div class="card card-bordered">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Two-Factor Authentication</h4>
                                        <div class="nk-block-des">
                                            @if ($user->tr01_two_factor_method === 'google')
                                                <p>Open your Authenticator app and enter the 6-digit code to log in.</p>
                                            @elseif($user->tr01_two_factor_method === 'email')
                                                <p>We just sent a 6-digit code to your email:
                                                    <strong>{{ preg_replace('/(?<=...).(?=.*@)/', '*', $user->tr01_email) }}</strong>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('auth.2fa.verify') }}" method="POST">
                                    @csrf

                                    @if (session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    @if (session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="code">Authentication Code or Recovery
                                                Code</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <input type="text"
                                                class="form-control form-control-lg text-center fs-20px font-weight-bold"
                                                id="code" name="code" placeholder="Enter Code" required
                                                autofocus
                                                oninput="this.value = this.value.replace(/[^0-9a-zA-Z-]/g, '');">
                                        </div>
                                    </div>
                                    <div class="form-group mt-4">
                                        <button class="btn btn-lg btn-primary btn-block">Verify</button>
                                    </div>
                                </form>
                                <div class="form-note-s2 text-center pt-4">
                                    <a href="{{ route('user_login') }}">Cancel and return to login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="{{ asset('backAssets/assets/js/bundle.js?ver=3.2.0') }}"></script>
    <script src="{{ asset('backAssets/assets/js/scripts.js?ver=3.2.0') }}"></script>

</body>

</html>
