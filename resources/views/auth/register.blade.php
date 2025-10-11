<x-layouts.app :title="'Sign up'">
    <div class="card card-center">
        <h2 style="margin:0 0 1.5rem 0; text-align:center; font-size:1.75rem;">SIGN UP</h2>
        <form method="post" action="{{ route('auth.register.post') }}">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:.4rem; font-weight:600; font-size:.8rem; color:var(--text-main); text-transform:uppercase;">Email</label>
                <input type="email" name="email" required style="padding:.75rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:.4rem; font-weight:600; font-size:.8rem; color:var(--text-main); text-transform:uppercase;">No HP</label>
                <input type="text" name="phone" style="padding:.75rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:.4rem; font-weight:600; font-size:.8rem; color:var(--text-main); text-transform:uppercase;">Nama Lengkap</label>
                <input type="text" name="name" required style="padding:.75rem;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:.4rem; font-weight:600; font-size:.8rem; color:var(--text-main); text-transform:uppercase;">Password</label>
                <input type="password" name="password" required style="padding:.75rem; margin-bottom:.75rem;">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required style="padding:.75rem;">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:.75rem; font-size:1rem;">Sign Up</button>
        </form>
        @if ($errors->any())
            <ul style="color:var(--red); margin-top:.75rem; font-size:.9rem; padding-left:1.25rem;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.app>


