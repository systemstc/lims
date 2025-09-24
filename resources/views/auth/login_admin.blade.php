<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../../../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ asset('backAssets/images/favicon.png') }}">
    <!-- Page Title  -->
    <title>Login | LIMS</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('backAssets/css/theme.css') }}">
    <style>
        .lims-brand-text {
            font-weight: 700;
            font-size: 1.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #2c3e50;
            /* fallback */
            text-shadow: 3px 1px 1px rgba(0, 0, 0, 0.2);
        }

        /* Optional gradient style */
        .text-gradient {
            background: linear-gradient(90deg, #f52601, #3F41D1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="nk-body bg-white npc-default pg-auth">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        <div class="brand-logo text-center">
                            <a href="javaScript:void(0)" class="logo-link">
                                <img class="logo-small logo-img logo-img-small me-2"
                                    src="{{ asset('backAssets/images/logo.png') }}" alt="logo-small">
                                <span class="lims-brand-text text-gradient">LIMS</span>
                            </a>
                        </div>
                        <div class="card">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Sign-In</h4>
                                        <div class="nk-block-des">
                                            <p>Access the Dashboard using your email and password.</p>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('admin_login') }}" class="form-validate is-alter" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="txt_email">Email</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg" id="txt_email"
                                                name="txt_email" placeholder="Enter your email address">
                                            @error('txt_email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="txt_password">Password</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <a href="#" class="form-icon form-icon-right passcode-switch lg"
                                                data-target="txt_password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control form-control-lg"
                                                id="txt_password" name="txt_password" placeholder="Enter your passcode">
                                            @error('txt_password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="nk-footer nk-auth-footer-full">
                        <div class="container wide-lg">
                            <div class="row g-3">
                                <div class="text-center">
                                    <div class="nk-block-content text-center text-lg-left">
                                        <p class="text-soft">&copy; 2023 System Cell. All Rights Reserved.</p>
                                    </div>
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
    <script src="{{ asset('backAssets/js/bundle.js') }}"></script>
    <script src="{{ asset('backAssets/js/scripts.js') }}"></script>
</body>

</html>
