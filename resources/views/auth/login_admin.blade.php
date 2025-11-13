<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    <title>Admin Login | Textile Testing LIMS</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-wrapper {
            width: 100%;
            display: flex;
            min-height: 100vh;
        }

        /* Left side - Branding */
        .branding-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .bg-icon {
            position: absolute;
            font-size: 180px;
            opacity: 0.1;
            top: 17%;
            right: 10%;
            z-index: 0;
            animation: float 7s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(15px);
            }
        }

        .brand-logo-container {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 40px;
            z-index: 2;
            position: relative;
        }

        .logo-box {
            width: 90px;
            height: 90px;
            background: #fff;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .logo-box img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .brand-text h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 6px;
            background: linear-gradient(135deg, #fff 0%, #dcd6f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-text p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .brand-content {
            z-index: 2;
            position: relative;
        }

        .brand-content h2 {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #fff 0%, #dcd6f7 100%);
             -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-content p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.5;
        }

        .brand-footer {
            position: relative;
            z-index: 2;
            margin-top: 40px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }

        /* Right side - Login */
        .login-section {
            flex: 1;
            background: white;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .login-container {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
            position: relative;
        }

        .form-header {
            margin-bottom: 35px;
        }

        .form-header h3 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .form-header p {
            font-size: 0.95rem;
            color: #666;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            font-size: 0.95rem;
            border: 2px solid #e8e8f0;
            border-radius: 6px;
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            font-size: 1rem;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            font-size: 0.95rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .btn-submit:hover {
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
            transform: translateY(-2px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        @media (max-width: 900px) {
            .page-wrapper {
                flex-direction: column;
            }

            .branding-section {
                display: none;
            }

            .login-section {
                padding: 40px 25px;
            }
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <!-- Left Section -->
        <div class="branding-section">
            <div class="bg-icon">🔬</div>

            <div class="brand-logo-container">
                <div class="logo-box">
                    <img src="{{ asset('frontAssets/textiles_logo_200.png') }}" alt="Textile Committee Logo">
                </div>
                <div class="brand-text">
                    <h2>Laboratory Information Management System</h2>
                </div>
            </div>

            <div class="brand-content">
                <h2>Welcome Admin!</h2>
                <p>Log in to manage users, oversee laboratory operations, and control administrative tasks with ease.
                </p>
            </div>

            <div class="brand-footer">
                <p>&copy; 2024 System Cell. All Rights Reserved.</p>
            </div>
        </div>

        <!-- Right Section -->
        <div class="login-section">
            <div class="login-container">
                <div class="form-header">
                    <h3>Admin Sign In</h3>
                </div>

                <form method="POST" action="{{ route('admin_login') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="txt_email">Email Address</label>
                        <div class="form-control-wrap">
                            <input type="email" class="form-control" id="txt_email" name="txt_email"
                                placeholder="Enter your email" required>
                            @error('txt_email')
                                <span class="error-message text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="txt_password">Password</label>
                        <div class="form-control-wrap" style="position: relative;">
                            <input type="password" class="form-control" id="txt_password" name="txt_password"
                                placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">👁</button>
                            @error('txt_password')
                                <span class="error-message text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Sign In</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('txt_password');
            const btn = event.currentTarget;
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '👁‍🗨';
            } else {
                input.type = 'password';
                btn.textContent = '👁';
            }
        }
    </script>
</body>

</html>
