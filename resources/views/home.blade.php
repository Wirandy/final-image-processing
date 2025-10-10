<x-layouts.app :title="'Home'">
    <div class="card card-hero">
        <img src="{{ asset('logo.png') }}" alt="logo" class="logo-large">
        <h2 style="margin:0 0 1rem 0;">Di era digital, setiap detail berharga. Platform ini dirancang untuk menvisualisasi interpretasi pencitraan medis, meningkatkan keakurasian, dan mempercepat proses klinis.</h2>
        <form method="get" action="{{ route('patients.index') }}" style="max-width:500px; margin:1.5rem auto 0;">
            <input type="search" name="q" placeholder="CARI">
        </form>
    </div>
    <div class="card" style="max-width:900px; margin:2rem auto;">
        <table style="width:100%;">
            <thead>
                <tr>
                    <th>Column 1</th>
                    <th>Column 2</th>
                    <th>Column 3</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            </tbody>
        </table>
    </div>
</x-layouts.app>

