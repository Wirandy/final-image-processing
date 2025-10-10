<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Forensics Imaging' }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        body { background: url('{{ asset('assets/bg.jpg') }}') no-repeat center center fixed; background-size: cover; }
        .backdrop { backdrop-filter: blur(6px); background-color: rgba(17,24,39,.8); min-height:100vh; }
        .brand { display:flex; align-items:center; gap:.5rem; }
        .brand img { height:28px; }
        a.nav { color:#fff; text-decoration:none; margin-left: .75rem; }
    </style>
    @stack('head')
    @vite([])
    @csrf
</head>
<body>
<div class="backdrop">
    <header>
        <div class="container">
            <div class="header-content">
                <div class="brand">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo">
                    <div>
                        <h1 style="margin:0">AIFI Imaging</h1>
                        <p class="subtitle" style="margin:0">Advanced Forensic Imaging</p>
                    </div>
                </div>
                <nav>
                    <a class="nav" href="{{ route('patients.index') }}">Patients</a>
                    @auth
                        <span class="nav">Hi, {{ auth()->user()->name }}</span>
                        <a class="nav" href="{{ route('auth.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" method="post" action="{{ route('auth.logout') }}" style="display:none;">@csrf</form>
                    @else
                        <a class="nav" href="{{ route('auth.login') }}">Login</a>
                        <a class="nav" href="{{ route('auth.register') }}">Sign up</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main class="container" style="padding-bottom:3rem;">
        {{ $slot }}
    </main>
</div>

@stack('scripts')
</body>
</html>


