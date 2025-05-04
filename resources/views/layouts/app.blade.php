<!DOCTYPE html>
<html>
<head>
    <title>E-PON Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-image: url('{{ asset('images/background2.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: #004e00;
            height: 38px;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: white;
        }

        .logo3 {
            display: block;
            margin-left: 20px;
            margin-top: 10px;
            height: 32px;
            position: static;
        }

        .btn-primary {
            background-color: #008000;
            border-color: #008000;
        }

        .btn-primary:hover {
            background-color: #004e00;
            border-color: #004e00;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-custom">
        <img src="{{ asset('images/logo3.png') }}" alt="Logo" class="logo3 mb-3">
        <div class="container">
            <a class="navbar-brand" href="#">Welcome, {{ Auth::check() ? Auth::user()->name : 'Guest' }}!</a>

            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('trackers.index') }}">My Tracker list</a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-link nav-link" type="submit">Logout</button>
                            </form>
                        </li>
                    @endauth
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        @yield('content')
    </div>

    {{-- JS Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> {{-- Chart.js CDN --}}
    @yield('scripts') {{-- Blade section for child view scripts --}}
</body>
</html>
