<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ភ្លេចពាក្យសម្ងាត់</title>
    <link rel="icon" type="image/png" href="{{ asset('Uploads/products/Yotta_Icon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Kantumruy Pro', sans-serif;
            background: #f1f5f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background: #38bdf8;
            border: none;
            padding: 10px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #0ea5e9;
        }
    </style>
</head>

<body>
    <div class="card p-4">
        <h3 class="fw-bold text-center mb-3">ភ្លេចពាក្យសម្ងាត់?</h3>
        <p class="text-muted text-center small mb-4">បញ្ចូលអ៊ីមែលរបស់អ្នក ហើយយើងនឹងផ្ញើកូដ OTP មក។</p>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="fw-bold small text-muted">អាសយដ្ឋានអ៊ីមែល</label>
                <input type="email" name="email" class="form-control" placeholder="" required>
            </div>
            <button type="submit" class="btn btn-outline-primary w-100 mb-3">ផ្ញើកូដ OTP</button>
            <a href="{{ route('login') }}"
                class="d-block text-center text-decoration-none text-muted small">ត្រឡប់ទៅចូលប្រើ</a>
        </form>
    </div>
</body>

</html>
