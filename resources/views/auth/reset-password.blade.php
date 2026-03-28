<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>កំណត់ពាក្យសម្ងាត់ថ្មី</title>
    <link rel="icon" type="image/png" href="{{ asset('Uploads/products/Yotta_Icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f5f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: sans-serif;
            font-family: 'Kantumruy Pro', sans-serif;

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
    </style>
</head>

<body>
    <div class="card p-4">
        <h3 class="fw-bold text-center mb-3">កំណត់ពាក្យសម្ងាត់ថ្មី</h3>
        <p class="text-muted text-center small mb-4">សូមពិនិត្យអ៊ីមែលរបស់អ្នកសម្រាប់កូដ OTP។</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label class="fw-bold small text-muted">កូដ OTP</label>
                <input type="text" name="otp" class="form-control text-center fw-bold letter-spacing-2"
                    style="letter-spacing: 5px; font-size: 1.2rem;" required>
            </div>

            <div class="mb-3">
                <label class="fw-bold small text-muted">ពាក្យសម្ងាត់ថ្មី</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="fw-bold small text-muted">បញ្ជាក់ពាក្យសម្ងាត់</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-outline-primary w-100">កំណត់ពាក្យសម្ងាត់</button>
        </form>
    </div>
</body>

</html>
