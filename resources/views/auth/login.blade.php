<x-layouts.app :title="'Login'">
    <div class="card" style="max-width:420px; margin: 2rem auto;">
        <h3 style="color:#fff; margin-top:0;">Login</h3>
        <form method="post" action="{{ route('auth.login.post') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required style="width:100%; margin-bottom:.5rem;">
            <input type="password" name="password" placeholder="Password" required style="width:100%; margin-bottom:.5rem;">
            <label style="color:#D1D5DB;"><input type="checkbox" name="remember"> Remember me</label>
            <button type="submit" class="process-button" style="width:100%; margin-top:.75rem;">Sign in</button>
        </form>
        @error('email')<p style="color:#F87171;">{{ $message }}</p>@enderror
    </div>
</x-layouts.app>


