<x-layouts.app :title="'Patients'">
    <div class="card" style="max-width:900px; margin:2rem auto;">
        <form method="get" action="{{ route('patients.index') }}" style="margin:0;">
            <input type="search" name="q" placeholder="Search patient name or ID" value="{{ $q }}" style="box-sizing:border-box;">
        </form>
    </div>

    @auth
    <div class="card" style="max-width:900px; margin:2rem auto;">
        <h3 style="margin-top:0;">New Patient</h3>
        <form method="post" action="{{ route('patients.store') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div>
                    <label style="display:block; margin-bottom:.4rem; font-weight:600; font-size:.85rem; text-transform:uppercase;">Name</label>
                    <input name="name" required style="box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:.4rem; font-weight:600; font-size:.85rem; text-transform:uppercase;">Identifier (Email)</label>
                    <input name="identifier" type="email" style="box-sizing:border-box;">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:1.25rem; width:auto;">Save Patient</button>
        </form>
    </div>
    @endauth

    <div class="card" style="max-width:900px; margin:2rem auto;">
        <h3 style="margin-top:0;">Results</h3>
        <table style="width:100%; color:var(--text-main);">
            <thead><tr><th style="text-align:left;">Name</th><th style="text-align:left;">Email</th><th style="text-align:center;">Actions</th></tr></thead>
            <tbody>
            @foreach($patients as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->identifier }}</td>
                    <td style="text-align:center;"><a class="btn btn-primary" style="display:inline-block; width:auto; text-decoration:none; font-size:.85rem; padding:.35rem .7rem;" href="{{ route('patients.show', $p) }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="margin-top:.5rem;">{{ $patients->links() }}</div>
    </div>
</x-layouts.app>


