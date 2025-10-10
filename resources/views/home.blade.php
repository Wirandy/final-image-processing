<x-layouts.app :title="'Home'">
    <div class="card" style="background: rgba(55,65,81,.6);">
        <div style="display:flex; align-items:center; gap:1rem;">
            <img src="{{ asset('assets/logo.png') }}" alt="logo" style="height:64px;">
            <div>
                <h3 style="margin:0; color:#fff;">Di era digital, setiap detail berharga.</h3>
                <p style="margin:.25rem 0; color:#D1D5DB; max-width:60ch;">Platform ini dirancang untuk menvisualisasi interpretasi pencitraan medis, meningkatkan keakurasian, dan mempercepat proses klinis.</p>
            </div>
        </div>
        <form method="get" action="{{ route('patients.index') }}" style="margin-top:1rem;">
            <input type="search" name="q" placeholder="Cari pasien..." style="width:100%; border-radius:9999px;">
        </form>
    </div>
    <div class="card" style="min-height:200px;">
        <h3 style="color:#fff; margin-top:0;">Data Terbaru</h3>
        <p style="color:#9CA3AF;">Buka menu Patients untuk melihat daftar lengkap.</p>
    </div>
</x-layouts.app>

