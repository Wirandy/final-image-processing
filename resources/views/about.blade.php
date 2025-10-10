<x-layouts.app :title="'About Us'">
    <div class="card card-hero">
        <img src="{{ asset('logo.png') }}" alt="logo" class="logo-large">
        <h2 style="margin:0 0 .5rem 0;">ABOUT US</h2>
        <p style="margin:0; color:var(--text-sec); max-width:70ch; margin:0 auto;">Di era digital, setiap detail berharga. Platform ini dirancang untuk menvisualisasi interpretasi pencitraan medis, meningkatkan keakurasian, dan mempercepat proses klinis. Tim kami fokus pada akurasi, UX, dan percepatan workflow klinis.</p>
    </div>
    <div class="card" style="max-width:900px; margin:2rem auto;">
        <h3 style="margin-top:0; text-align:center;">OUR TEAM</h3>
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem;">
            <div style="background:#fff; border:1px solid var(--gray-300); border-radius:12px; height:180px;"></div>
            <div style="background:#fff; border:1px solid var(--gray-300); border-radius:12px; height:180px;"></div>
            <div style="background:#fff; border:1px solid var(--gray-300); border-radius:12px; height:180px;"></div>
            <div style="background:#fff; border:1px solid var(--gray-300); border-radius:12px; height:180px;"></div>
        </div>
    </div>
</x-layouts.app>

