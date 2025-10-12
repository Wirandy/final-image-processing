<x-layouts.app :title="'Home'">
    <div class="card card-hero">
        <img src="{{ asset('logo.png') }}" alt="logo" class="logo-large">
        <h2 style="margin:0 0 1rem 0;">Di era digital, setiap detail berharga. Platform ini dirancang untuk menvisualisasi interpretasi pencitraan medis, meningkatkan keakurasian, dan mempercepat proses klinis.</h2>
        <form method="get" action="{{ route('patients.index') }}" style="max-width:500px; margin:1.5rem auto 0;">
            <input type="search" name="q" placeholder="CARI PASIEN">
        </form>
    </div>

    <!-- AI Forensic Analysis Feature Highlight -->
    <div class="card" style="max-width:900px; margin:2rem auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
        <div style="text-align: center;">
            <h2 style="margin: 0 0 1rem 0; font-size: 2rem;">🔬 AI-Powered Forensic Analysis</h2>
            <p style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 1.5rem; opacity: 0.95;">
                Teknologi AI terbaru untuk analisis forensik medis otomatis. Deteksi cedera, penilaian keparahan, dan prediksi penyebab dengan akurasi tinggi.
            </p>
            <a href="{{ route('patients.index') }}" class="btn btn-primary" style="background: white; color: #667eea; font-weight: bold; text-decoration: none; display: inline-block; padding: 0.75rem 2rem; border-radius: 8px; font-size: 1.1rem;">
                Mulai Analisis →
            </a>
        </div>
    </div>

    <!-- Features Grid -->
    <div style="max-width:1100px; margin:2rem auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div class="card" style="text-align: center; padding: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🩺</div>
            <h3 style="margin: 0 0 0.5rem 0; color: var(--primary);">Injury Classification</h3>
            <p style="color: var(--text-sec); line-height: 1.6;">Deteksi otomatis jenis cedera: Fracture, Bruise, Burn, Laceration</p>
        </div>

        <div class="card" style="text-align: center; padding: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📊</div>
            <h3 style="margin: 0 0 0.5rem 0; color: var(--primary);">Severity Assessment</h3>
            <p style="color: var(--text-sec); line-height: 1.6;">Penilaian tingkat keparahan: Ringan, Sedang, Parah</p>
        </div>

        <div class="card" style="text-align: center; padding: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
            <h3 style="margin: 0 0 0.5rem 0; color: var(--primary);">Cause Prediction</h3>
            <p style="color: var(--text-sec); line-height: 1.6;">Prediksi penyebab cedera berdasarkan pola AI</p>
        </div>

        <div class="card" style="text-align: center; padding: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">⚰️</div>
            <h3 style="margin: 0 0 0.5rem 0; color: var(--primary);">Post-Mortem Detection</h3>
            <p style="color: var(--text-sec); line-height: 1.6;">Identifikasi artifact dan pola post-mortem</p>
        </div>
    </div>

    <!-- How It Works -->
    <div class="card" style="max-width:900px; margin:2rem auto;">
        <h2 style="text-align: center; margin-top: 0; color: var(--primary);">Cara Menggunakan</h2>
        <div style="display: grid; gap: 1.5rem; margin-top: 2rem;">
            <div style="display: flex; gap: 1rem; align-items: start;">
                <div style="background: var(--primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">1</div>
                <div>
                    <h4 style="margin: 0 0 0.5rem 0;">Login & Buat Patient</h4>
                    <p style="color: var(--text-sec); margin: 0;">Daftar atau login, lalu tambahkan data pasien baru</p>
                </div>
            </div>
            <div style="display: flex; gap: 1rem; align-items: start;">
                <div style="background: var(--primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">2</div>
                <div>
                    <h4 style="margin: 0 0 0.5rem 0;">Upload Medical Image</h4>
                    <p style="color: var(--text-sec); margin: 0;">Upload gambar X-ray, CT scan, atau medical image lainnya</p>
                </div>
            </div>
            <div style="display: flex; gap: 1rem; align-items: start;">
                <div style="background: var(--primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">3</div>
                <div>
                    <h4 style="margin: 0 0 0.5rem 0;">Run AI Analysis</h4>
                    <p style="color: var(--text-sec); margin: 0;">Pilih "🔬 AI Forensic Analysis" di control panel dan tunggu hasil</p>
                </div>
            </div>
            <div style="display: flex; gap: 1rem; align-items: start;">
                <div style="background: var(--primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">4</div>
                <div>
                    <h4 style="margin: 0 0 0.5rem 0;">View Results</h4>
                    <p style="color: var(--text-sec); margin: 0;">Lihat annotated image, injury count, severity, dan forensic summary</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

