<x-layouts.app :title="'Patients'">
    <div class="card">
        <form method="get" action="{{ route('patients.index') }}" style="margin:0;">
            <input type="search" name="q" placeholder="Search patient name or ID" value="{{ $q }}" style="width:100%;">
        </form>
    </div>

    @auth
    <div class="card">
        <h3 style="color:#fff; margin-top:0;">New Patient</h3>
        <form method="post" action="{{ route('patients.store') }}">
            @csrf
            <div class="row" style="gap:.5rem;">
                <input name="name" placeholder="Name" required style="flex:1;">
                <input name="identifier" placeholder="Identifier (optional)" style="flex:1;">
            </div>
            <textarea name="notes" placeholder="Notes" style="width:100%; margin-top:.5rem;"></textarea>
            <button type="submit" class="process-button" style="margin-top:.5rem; width:auto;">Save</button>
        </form>
    </div>
    @endauth

    <div class="card">
        <h3 style="color:#fff; margin-top:0;">Results</h3>
        <table style="width:100%; color:#D1D5DB;">
            <thead><tr><th style="text-align:left;">Name</th><th style="text-align:left;">Identifier</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach($patients as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->identifier }}</td>
                    <td><a class="process-button" style="display:inline-block; width:auto; text-decoration:none;" href="{{ route('patients.show', $p) }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="margin-top:.5rem;">{{ $patients->links() }}</div>
    </div>
</x-layouts.app>


