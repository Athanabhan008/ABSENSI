<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="76x76" href="admin/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="admin/assets/img/logos/Logo MBS Corp.png">
    <title>ABSENSI</title>
    <style>
        /* CSS Reset dan Variabel */
        :root {
            --primary-blue: #003C8D;
            --primary-cyan: #5CE1E6;
            --cyan-light: rgba(92, 225, 230, 0.1);
            --cyan-medium: rgba(92, 225, 230, 0.3);
            --blue-dark: #002a63;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #003C8D 0%, #5CE1E6 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Background Decorative Elements */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(92, 225, 230, 0.1);
            border-radius: 50%;
            top: -250px;
            right: -250px;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(0, 60, 141, 0.2);
            border-radius: 50%;
            bottom: -200px;
            left: -200px;
            z-index: 0;
        }

        /* Container Utama */
        .login-container {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 60, 141, 0.3),
                        0 0 0 1px rgba(92, 225, 230, 0.2);
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
        }

        /* Brand Logo */
        .brand {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand h1 {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #003C8D 0%, #5CE1E6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .brand p {
            color: #666;
            font-size: 14px;
        }

        /* Login Header */
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-header h2 {
            color: var(--primary-blue);
            font-size: 26px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .login-header p {
            color: #777;
            font-size: 14px;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--primary-blue);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-cyan);
            font-size: 18px;
            z-index: 1;
        }

        .form-group input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: var(--transition);
            background: #f8f9fa;
            color: #333;
        }

        .form-group input:focus {
            border-color: var(--primary-cyan);
            background: white;
            box-shadow: 0 0 0 4px var(--cyan-light);
            outline: none;
        }

        .form-group input::placeholder {
            color: #999;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--primary-blue);
            font-size: 20px;
            z-index: 2;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--primary-cyan);
            transform: translateY(-50%) scale(1.1);
        }

        .error-message {
            color: var(--error-color);
            font-size: 13px;
            margin-top: 8px;
            display: none;
            padding-left: 4px;
        }

        /* Login Button */
        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #003C8D 0%, #5CE1E6 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(0, 60, 141, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 60, 141, 0.4);
        }

        .login-button:active {
            transform: translateY(0);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0;
            color: #999;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }

        .divider::before {
            margin-right: 10px;
        }

        .divider::after {
            margin-left: 10px;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .login-footer p {
            color: #777;
            font-size: 13px;
        }

        /* Responsivitas */
        @media (max-width: 600px) {
            .login-container {
                padding: 40px 30px;
                border-radius: 20px;
            }

            .brand h1 {
                font-size: 28px;
            }

            .login-header h2 {
                font-size: 22px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .login-container {
                padding: 35px 25px;
            }

            .form-group input {
                padding: 14px 14px 14px 44px;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Brand -->
        <div class="brand">
            <h1>ABSENSI MBS</h1>
        </div>

            {{-- <img src="{{ asset('admin/assets/img/logos/Logo MBS Corp.png') }}" width="100px" alt="" style="position: relative; left: 130px; margin-bottom: 40px;"> --}}

        <!-- Login Header -->
        <div class="login-header">
            <h2>Selamat Datang</h2>
            <p>Masuk ke akun Anda untuk melanjutkan!</p>
        </div>

        <!-- Form Login -->
        <form role="form" id="loginForm" method="POST" action="">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="text" id="username" name="name" value="{{ old('name') }}" placeholder="Masukkan username Anda" required>
                </div>
                <div class="error-message" id="usernameError">Username harus diisi</div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                    <span class="password-toggle" id="passwordToggle">👁️</span>
                </div>
                <div class="error-message" id="passwordError">Password harus diisi</div>
            </div>

            <button type="submit" class="login-button">Masuk</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');

            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.textContent = type === 'password' ? '👁️' : '🙈';
            });

            // Form validation
            const form = document.getElementById('loginForm');
            const usernameInput = document.getElementById('username');
            const passwordInputField = document.getElementById('password');
            const usernameError = document.getElementById('usernameError');
            const passwordError = document.getElementById('passwordError');

            form.addEventListener('submit', function(e) {
                let isValid = true;

                if (!usernameInput.value.trim()) {
                    usernameError.style.display = 'block';
                    usernameInput.style.borderColor = 'var(--error-color)';
                    isValid = false;
                } else {
                    usernameError.style.display = 'none';
                    usernameInput.style.borderColor = '#e0e0e0';
                }

                if (!passwordInputField.value.trim()) {
                    passwordError.style.display = 'block';
                    passwordInputField.style.borderColor = 'var(--error-color)';
                    isValid = false;
                } else {
                    passwordError.style.display = 'none';
                    passwordInputField.style.borderColor = '#e0e0e0';
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            // Remove error on input
            usernameInput.addEventListener('input', function() {
                if (this.value.trim()) {
                    usernameError.style.display = 'none';
                    this.style.borderColor = '#e0e0e0';
                }
            });

            passwordInputField.addEventListener('input', function() {
                if (this.value.trim()) {
                    passwordError.style.display = 'none';
                    this.style.borderColor = '#e0e0e0';
                }
            });
        });
    </script>
</body>
</html>
