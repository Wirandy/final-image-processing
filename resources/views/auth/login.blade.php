<x-layouts.app :title="'Login'">
    <div class="card card-center">
        <h2 style="margin:0 0 1.5rem 0; text-align:center; font-size:1.75rem;">LOGIN</h2>
        <form method="post" action="{{ route('auth.login.post') }}">
            @csrf
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:.4rem; font-weight:600; font-size:.8rem; color:var(--text-main); text-transform:uppercase;">Email</label>
                <input type="email" name="email" required style="padding:.75rem;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:.4rem; font-weight:600; font-size:.8rem; color:var(--text-main); text-transform:uppercase;">Password</label>
                <input type="password" name="password" required style="padding:.75rem;">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:.75rem; font-size:1rem;">Sign Up</button>
        </form>
        @error('email')<p style="color:var(--red); margin-top:.75rem; font-size:.9rem;">{{ $message }}</p>@enderror
    </div>
</x-layouts.app>


