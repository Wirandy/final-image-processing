<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Forensics Imaging' }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        body { background-image: url('{{ asset('latar_belakang.png') }}'); }
    </style>
    @stack('head')
    @vite([])
    @csrf
</head>
<body>
    <div class="page-wrapper">
        <header>
            <div class="container">
                <div class="header-content">
                    <div class="brand">
                        <img src="{{ asset('logo.png') }}" alt="Logo">
                        <div>
                            <h1 style="margin:0">AIFI Imaging</h1>
                            <p class="subtitle" style="margin:0">Advanced Forensic Imaging</p>
                        </div>
                    </div>
                    <nav style="display:flex; align-items:center; gap:1.5rem;">
                        <div style="display:flex; gap:.5rem;">
                            <a class="nav" href="{{ route('home') }}">HOME</a>
                            <a class="nav" href="{{ route('about') }}">ABOUT US</a>
                            <a class="nav" href="{{ route('qna') }}">QNA</a>
                        </div>
                        <div style="display:flex; align-items:center; gap:.75rem; margin-left:1rem; padding-left:1rem; border-left:2px solid var(--gray-300);">
                            @auth
                                <span style="color:var(--text-sec); font-size:.9rem; font-weight:600;">Hi, {{ auth()->user()->name }}</span>
                                <a class="btn btn-danger" href="{{ route('auth.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                <form id="logout-form" method="post" action="{{ route('auth.logout') }}" style="display:none;">@csrf</form>
                            @else
                                <a class="btn btn-outline" href="{{ route('auth.login') }}">Login</a>
                                <a class="btn btn-primary" href="{{ route('auth.register') }}">Sign Up</a>
                            @endauth
                        </div>
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


