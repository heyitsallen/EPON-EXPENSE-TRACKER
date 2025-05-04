<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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
            height: 150px;
        }

        .card {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<!-- ✅ Logo + Register Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <!-- ✅ Centered Logo -->
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo mb-3">

            <div class="card p-4">
                <h2 class="mb-4 text-center">Register</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required value="{{ old('email') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}">Already have an account? Login here</a>
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
