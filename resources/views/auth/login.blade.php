<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Easy - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-dark: #0d4f3c;
            --green-main: #1a7a57;
            --green-mid: #22a06b;
            --green-light: #4cde9e;
            --green-pale: #e8f8f1;
            --accent-gold: #f4c23b;
            --accent-teal: #0ea5a0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            overflow-x: hidden;
        }

        /* Left Panel - Background Image */
        .left-panel {
            flex: 1;
            background: 
                linear-gradient(135deg, rgba(13,79,60,0.88) 0%, rgba(26,122,87,0.80) 50%, rgba(14,165,160,0.75) 100%),
                url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2072&auto=format&fit=crop') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at center, rgba(76,222,158,0.1) 0%, transparent 60%);
            animation: pulse 6s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        .brand-logo {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            letter-spacing: -1px;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .brand-logo span {
            color: var(--green-light);
        }

        .brand-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            position: relative;
            z-index: 1;
        }

        .brand-tagline {
            color: rgba(255,255,255,0.9);
            font-size: 1rem;
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            z-index: 1;
        }

        .feature-list {
            list-style: none;
            width: 100%;
            max-width: 380px;
            position: relative;
            z-index: 1;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .feature-list li:last-child { border-bottom: none; }

        .feature-icon {
            width: 36px;
            height: 36px;
            background: rgba(76,222,158,0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--green-light);
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .floating-card {
            position: absolute;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            padding: 12px 16px;
            color: white;
            font-size: 0.75rem;
            animation: float 4s ease-in-out infinite;
        }

        .fc-1 { top: 15%; right: 8%; animation-delay: 0s; }
        .fc-2 { bottom: 20%; left: 5%; animation-delay: 2s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        /* Right Panel - Login Form */
        .right-panel {
            width: 480px;
            min-width: 480px;
            background: #fafffe;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px 50px;
            overflow-y: auto;
        }

        .login-header {
            margin-bottom: 36px;
        }

        .login-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--green-dark);
            margin-bottom: 6px;
        }

        .login-header p {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 28px;
        }

        .role-btn {
            padding: 10px 8px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            font-family: 'Poppins', sans-serif;
        }

        .role-btn:hover {
            border-color: var(--green-mid);
            background: var(--green-pale);
        }

        .role-btn.active {
            border-color: var(--green-main);
            background: var(--green-pale);
            box-shadow: 0 0 0 3px rgba(26,122,87,0.1);
        }

        .role-btn .role-icon {
            font-size: 1.4rem;
            display: block;
            margin-bottom: 4px;
        }

        .role-btn .role-name {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--green-dark);
        }

        /* Role colors */
        .role-btn[data-role="admin"].active { border-color: #dc2626; background: #fef2f2; }
        .role-btn[data-role="manager"].active { border-color: #d97706; background: #fffbeb; }
        .role-btn[data-role="executive_accountant"].active { border-color: var(--green-main); background: var(--green-pale); }
        .role-btn[data-role="auditor"].active { border-color: #0369a1; background: #eff6ff; }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.2s;
            background: white;
        }

        .form-control:focus {
            border-color: var(--green-main);
            box-shadow: 0 0 0 3px rgba(26,122,87,0.1);
            outline: none;
        }

        .input-group .form-control {
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .input-group-text {
            background: white;
            border: 2px solid #e5e7eb;
            border-left: none;
            border-radius: 0 10px 10px 0;
            cursor: pointer;
            color: #6b7280;
            transition: color 0.2s;
        }

        .input-group-text:hover { color: var(--green-main); }

        .input-group:focus-within .form-control,
        .input-group:focus-within .input-group-text {
            border-color: var(--green-main);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--green-main), var(--green-dark));
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, var(--green-dark), var(--green-main));
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26,122,87,0.35);
        }

        .btn-login:active { transform: translateY(0); }

        .forgot-link {
            color: var(--green-main);
            text-decoration: none;
            font-size: 0.83rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover { color: var(--green-dark); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0;
            color: #9ca3af;
            font-size: 0.8rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .demo-credentials {
            background: var(--green-pale);
            border: 1px solid #a7f3d0;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 0.78rem;
        }

        .demo-credentials .demo-title {
            font-weight: 700;
            color: var(--green-dark);
            margin-bottom: 8px;
            font-size: 0.8rem;
        }

        .demo-row {
            display: flex;
            justify-content: space-between;
            color: #374151;
            padding: 3px 0;
        }

        .demo-row span:first-child { color: #6b7280; }
        .demo-row code {
            background: white;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            color: var(--green-dark);
            border: 1px solid #d1fae5;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
            color: #dc2626;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .alert-success {
            background: var(--green-pale);
            border: 1px solid #a7f3d0;
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--green-dark);
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .left-panel {
                min-height: 220px;
                padding: 30px 20px;
                flex: none;
            }
            .feature-list, .floating-card { display: none; }
            .brand-icon { width: 60px; height: 60px; font-size: 1.8rem; margin-bottom: 12px; }
            .brand-logo { font-size: 2rem; }
            .right-panel {
                width: 100%;
                min-width: unset;
                padding: 30px 24px;
            }
        }
    </style>
</head>
<body>

    <!-- Left Panel -->
    <div class="left-panel">
        <div class="brand-icon">💼</div>
        <div class="brand-logo">Account<span>Easy</span></div>
        <p class="brand-tagline">Smart Accounting for Modern Business</p>

        <ul class="feature-list">
            <li>
                <span class="feature-icon"><i class="fas fa-building"></i></span>
                <span>Multi-company support with data isolation</span>
            </li>
            <li>
                <span class="feature-icon"><i class="fas fa-shield-alt"></i></span>
                <span>Role-based access control & security</span>
            </li>
            <li>
                <span class="feature-icon"><i class="fas fa-check-circle"></i></span>
                <span>Approval workflow & audit trail</span>
            </li>
            <li>
                <span class="feature-icon"><i class="fas fa-chart-bar"></i></span>
                <span>Real-time financial reports & analytics</span>
            </li>
            <li>
                <span class="feature-icon"><i class="fas fa-file-invoice"></i></span>
                <span>Complete AR/AP management system</span>
            </li>
        </ul>

        <!-- Floating Cards -->
        <div class="floating-card fc-1">
            <div style="font-weight:600;font-size:0.85rem;">✅ 4 User Roles</div>
            <div style="opacity:0.8;margin-top:2px;">Admin · Manager · Accountant · Auditor</div>
        </div>
        <div class="floating-card fc-2">
            <div style="font-weight:600;font-size:0.85rem;">🔒 Secure & Compliant</div>
            <div style="opacity:0.8;margin-top:2px;">Full audit trail logging</div>
        </div>
    </div>

    <!-- Right Panel - Login Form -->
    <div class="right-panel">
        <div class="login-header">
            <h2>Welcome Back 👋</h2>
            <p>Sign in to your Account Easy dashboard</p>
        </div>

        @if(session('success'))
            <div class="alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert-error"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Role Selector -->
        <div class="mb-3">
            <label class="form-label">Login As</label>
            <div class="role-selector">
                <button type="button" class="role-btn" data-role="executive_accountant" onclick="selectRole(this)">
                    <span class="role-icon">🧮</span>
                    <span class="role-name">Accountant</span>
                </button>
                <button type="button" class="role-btn" data-role="manager" onclick="selectRole(this)">
                    <span class="role-icon">👔</span>
                    <span class="role-name">Manager</span>
                </button>
                <button type="button" class="role-btn" data-role="admin" onclick="selectRole(this)">
                    <span class="role-icon">⚙️</span>
                    <span class="role-name">Admin</span>
                </button>
                <button type="button" class="role-btn" data-role="auditor" onclick="selectRole(this)">
                    <span class="role-icon">🔍</span>
                    <span class="role-name">Auditor</span>
                </button>
            </div>
            <small style="color:#9ca3af;font-size:0.75rem;"><i class="fas fa-info-circle me-1"></i>Role is for display only — your credentials determine access</small>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf
            <div class="mb-4">
                <label class="form-label" for="username">
                    <i class="fas fa-user me-1" style="color:var(--green-main)"></i> Username or Email
                </label>
                <input type="text" class="form-control @error('username') is-invalid @enderror"
                    id="username" name="username"
                    value="{{ old('username') }}"
                    placeholder="Enter your username or email"
                    autocomplete="username" required>
            </div>

            <div class="mb-2">
                <label class="form-label" for="password">
                    <i class="fas fa-lock me-1" style="color:var(--green-main)"></i> Password
                </label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password" required>
                    <span class="input-group-text" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember"
                        style="accent-color:var(--green-main)">
                    <label class="form-check-label" for="remember" style="font-size:0.82rem;color:#6b7280;">
                        Remember me
                    </label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i> Sign In
            </button>
        </form>

        {{-- Register & Developer links --}}
        <div style="margin-top:20px;display:flex;justify-content:space-between;align-items:center;font-size:.78rem">
            <a href="{{ route('register') }}" style="color:var(--green-main);font-weight:600;text-decoration:none">
                <i class="fas fa-building me-1"></i>Register New Company
            </a>
            <a href="{{ route('developer.login') }}" style="color:#9ca3af;text-decoration:none;font-size:.72rem">
                <i class="fas fa-code me-1"></i>Developer Portal
            </a>
        </div>

        <div class="divider">Demo Credentials</div>

        <div class="demo-credentials">
            <div class="demo-title"><i class="fas fa-key me-1"></i> Quick Test Accounts (Tan Sim Tax Advisory)</div>
            <div class="demo-row"><span>Accountant</span> <code>accountant_tansim</code> / <code>Account@1234</code></div>
            <div class="demo-row"><span>Manager</span> <code>manager_tansim</code> / <code>Manager@1234</code></div>
            <div class="demo-row"><span>Admin</span> <code>admin_tansim</code> / <code>Admin@1234</code></div>
            <div class="demo-row"><span>Auditor</span> <code>auditor_tansim</code> / <code>Auditor@1234</code></div>
        </div>

        <p style="text-align:center;margin-top:24px;font-size:0.75rem;color:#9ca3af;">
            &copy; {{ date('Y') }} Account Easy. All rights reserved.
        </p>
    </div>

    <script>
        function selectRole(btn) {
            document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }

        function togglePassword() {
            const pw = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pw.type === 'password') {
                pw.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pw.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Auto-fill username when role is selected
        const demoUsers = {
            'executive_accountant': 'accountant_tansim',
            'manager': 'manager_tansim',
            'admin': 'admin_tansim',
            'auditor': 'auditor_tansim',
        };

        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Uncomment to auto-fill:
                // document.getElementById('username').value = demoUsers[this.dataset.role] || '';
            });
        });
    </script>
</body>
</html>
