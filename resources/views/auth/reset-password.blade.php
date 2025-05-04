<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('{{ asset('images/background.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }

        .btn-primary {
            background-color: #008000;
            border-color: #008000;
        }

        .btn-primary:hover {
            background-color: #004e00;
            border-color: #004e00;
        }

        .logo {
            display: block;
            margin-left: auto;
            margin-right: auto;
            height: 80px;
        }

        .card {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo mb-3">

            <div class="card p-4">
                <h2 class="mb-4 text-center">Reset Password</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="password" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}">Back to Login</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

            <!-- Button to Welcome Page -->
            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="btn btn-secondary">Go to Welcome Page</a>
            </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
