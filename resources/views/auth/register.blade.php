<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - POS System</title>
    <link rel="icon" type="image/png" href="{{ asset('Uploads/products/Yotta_Icon.png') }}">


    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow: hidden;
            margin: 0;
            background-color: #fff;
            font-family: 'Kantumruy Pro', sans-serif;


        }

        /* --- LAYOUT CONTAINER --- */
        .login-wrapper {
            display: flex;
            height: 100%;
            width: 100%;
        }

        /* --- LEFT SIDE (BRANDING) --- */
        .brand-side {
            flex: 1;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        /* Decorative Circles */
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .c1 {
            width: 300px;
            height: 300px;
            top: -50px;
            left: -50px;
        }

        .c2 {
            width: 500px;
            height: 500px;
            bottom: -100px;
            right: -100px;
        }

        .brand-content {
            z-index: 2;
            text-align: center;
            padding: 40px;
        }

        .brand-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            color: #38bdf8;
            text-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* --- RIGHT SIDE (FORM) --- */
        .form-side {
            width: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 60px;
            background-color: #fff;
            overflow-y: auto;
            /* Allow scrolling if form is tall */
        }

        /* --- FORM ELEMENTS --- */
        .login-header {
            margin-bottom: 30px;
        }

        .login-header h2 {
            font-weight: 700;
            color: #1e293b;
        }

        .login-header p {
            color: #64748b;
            margin-top: 5px;
        }

        .input-group-text {
            background: transparent;
            border-right: none;
            color: #94a3b8;
        }

        .form-control {
            border-left: none;
            padding-left: 0;
            padding: 12px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #cbd5e1;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
            border-radius: 6px;
        }

        .input-group:focus-within .input-group-text {
            color: #38bdf8;
            border-color: #cbd5e1;
        }

        .btn-primary {
            background: #38bdf8;
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #0ea5e9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 900px) {
            .brand-side {
                display: none;
            }

            .form-side {
                width: 100%;
                padding: 30px;
            }

            body {
                overflow: auto;
            }

            /* Allow scrolling on mobile */
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <div class="brand-side">
            <div class="circle c1"></div>
            <div class="circle c2"></div>

            <div class="brand-content">
                <i class="fas fa-user-plus brand-icon"></i>
                <h1 class="fw-bold mb-3">ចូលរួមជាមួយប្រព័ន្ធយើងខ្ញុំ</h1>
                <p class="fs-5 text-white-50">បង្កើតគណនីរបស់អ្នក ដើម្បីចាប់ផ្តើម <br> គ្រប់គ្រងការលក់ថ្ងៃនេះ។</p>
            </div>
        </div>

        <div class="form-side">
            <div class="login-header">
                <h2>បង្កើតគណនី</h2>
                <p>សូមបញ្ចូលព័ត៌មានរបស់អ្នកខាងក្រោម ដើម្បីចាប់ផ្តើម។</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">ឈ្មោះពេញ</label>
                    <div class="input-group">
                        <input type="text" name="username" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">អាសយដ្ឋានអ៊ីមែល</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">ពាក្យសម្ងាត់</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">បញ្ជាក់ពាក្យសម្ងាត់</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-primary w-100 mb-3">បង្កើតគណនី</button>
            </form>

            <div class="text-center">
                <span class="text-muted small">មានគណនីរួចហើយ?</span>
                <a href="{{ route('login') }}" class="fw-bold text-dark text-decoration-none ms-1">ចូលប្រើ</a>
            </div>
            {{--
            <div class="text-center mt-4 text-muted small">
                &copy; {{ date('Y') }} ប្រព័ន្ធ POS. រក្សាសិទ្ធិគ្រប់យ៉ាង។
            </div> --}}
        </div>
    </div>

</body>

</html>
