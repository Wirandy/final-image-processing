<x-layouts.app :title="'About Us'">
    <div class="card" style="background: rgba(55,65,81,.6);">
        <div style="display:flex; align-items:center; gap:1rem;">
            <img src="{{ asset('assets/logo.png') }}" alt="logo" style="height:64px;">
            <div>
                <h3 style="margin:0; color:#fff;">ABOUT US</h3>
                <p style="margin:.25rem 0; color:#D1D5DB; max-width:70ch;">Platform ini dirancang untuk menvisualisasi interpretasi pencitraan medis. Tim kami fokus pada akurasi, UX, dan percepatan workflow klinis.</p>
            </div>
        </div>
        <h3 style="color:#fff;">OUR TEAM</h3>
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem;">
            <div class="card" style="height:140px;"></div>
            <div class="card" style="height:140px;"></div>
            <div class="card" style="height:140px;"></div>
            <div class="card" style="height:140px;"></div>
        </div>
    </div>
</x-layouts.app>

