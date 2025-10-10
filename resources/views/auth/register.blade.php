<x-layouts.app :title="'Sign up'">
    <div class="card" style="max-width:420px; margin: 2rem auto;">
        <h3 style="color:#fff; margin-top:0;">Sign up</h3>
        <form method="post" action="{{ route('auth.register.post') }}">
            @csrf
            <input type="text" name="name" placeholder="Name" required style="width:100%; margin-bottom:.5rem;">
            <input type="email" name="email" placeholder="Email" required style="width:100%; margin-bottom:.5rem;">
            <input type="password" name="password" placeholder="Password" required style="width:100%; margin-bottom:.5rem;">
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required style="width:100%; margin-bottom:.5rem;">
            <button type="submit" class="process-button" style="width:100%; margin-top:.75rem;">Create account</button>
        </form>
        @if ($errors->any())
            <ul style="color:#F87171;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.app>


