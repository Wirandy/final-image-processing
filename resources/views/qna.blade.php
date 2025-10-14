<x-layouts.app :title="'QnA'">
    <div class="card card-center" style="max-width: 900px; margin: 2rem auto;">
        <h2 style="margin-top:0; text-align:center; color: var(--primary);">❓ Frequently Asked Questions</h2>
        
        <div style="margin-top: 2rem; text-align: left;">
            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h3 style="margin: 0 0 1rem 0; color: var(--primary);">Apa itu AI Forensic Analysis?</h3>
                <p style="color: var(--text-sec); line-height: 1.8; margin: 0;">
                    AI Forensic Analysis adalah fitur analisis forensik medis berbasis kecerdasan buatan yang dapat mendeteksi cedera, menilai tingkat keparahan, memprediksi penyebab cedera, dan mengidentifikasi fitur post-mortem secara otomatis menggunakan teknologi CNN dan Roboflow API.
                </p>
            </div>

            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h3 style="margin: 0 0 1rem 0; color: var(--primary);">Cara Penggunaan Umum</h3>
                <ol style="color: var(--text-sec); line-height: 1.8; margin: 0; padding-left: 1.5rem;">
                    <li><strong>Daftar/Login</strong> → Buat akun atau masuk ke sistem</li>
                    <li><strong>Buka Patients</strong> → Klik menu "Patients" di navbar</li>
                    <li><strong>Tambah Pasien</strong> → Klik "Add New Patient" dan isi data</li>
                    <li><strong>Upload Gambar</strong> → Upload gambar medis (X-ray, CT scan, dll)</li>
                    <li><strong>Pilih Metode</strong> → Di Control Panel, pilih filter atau AI analysis</li>
                    <li><strong>Lihat Hasil</strong> → Hasil akan muncul di bagian Processed/Annotated</li>
                </ol>
            </div>

            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h3 style="margin: 0 0 1rem 0; color: var(--primary);">Cara Menggunakan AI Forensic Analysis</h3>
                <ol style="color: var(--text-sec); line-height: 1.8; margin: 0; padding-left: 1.5rem;">
                    <li>Login dan pilih/buat patient</li>
                    <li>Upload medical image (PNG/JPG/JPEG)</li>
                    <li>Klik tombol <strong>"Preview"</strong> pada gambar</li>
                    <li>Di panel FILTER, cari kategori <strong>"AI Forensic Analysis"</strong></li>
                    <li>Klik tombol <strong>"Forensic AI Analysis"</strong></li>
                    <li>Konfirmasi dialog yang muncul</li>
                    <li>Tunggu 5-15 detik untuk proses analisis</li>
                    <li>Lihat hasil: annotated image, injury count, severity, dan summary</li>
                </ol>
            </div>

            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h3 style="margin: 0 0 1rem 0; color: var(--primary);">Apa arti warna pada bounding box?</h3>
                <ul style="color: var(--text-sec); line-height: 1.8; margin: 0; padding-left: 1.5rem; list-style: none;">
                    <li><strong>Hijau</strong> = Cedera Ringan (area < 1000 px²)</li>
                    <li><strong>Kuning</strong> = Cedera Sedang (area 1000-3000 px²)</li>
                    <li><strong>Merah</strong> = Cedera Parah (area > 3000 px²)</li>
                </ul>
            </div>

            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h3 style="margin: 0 0 1rem 0; color: var(--primary);">Apa saja jenis cedera yang bisa dideteksi?</h3>
                <ul style="color: var(--text-sec); line-height: 1.8; margin: 0; padding-left: 1.5rem;">
                    <li><strong>Fracture</strong> - Patah tulang</li>
                    <li><strong>Bruise</strong> - Memar</li>
                    <li><strong>Burn</strong> - Luka bakar</li>
                    <li><strong>Laceration</strong> - Luka robek</li>
                </ul>
            </div>

            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h3 style="margin: 0 0 1rem 0; color: var(--primary);">Berapa lama proses analisis?</h3>
                <p style="color: var(--text-sec); line-height: 1.8; margin: 0;">
                    Proses AI Forensic Analysis biasanya memakan waktu <strong>5-15 detik</strong>, tergantung ukuran gambar dan koneksi internet. Sistem akan memanggil Roboflow API, memproses hasil, dan membuat annotated image.
                </p>
            </div>

            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h3 style="margin: 0 0 1rem 0; color: var(--primary);">Format gambar apa yang didukung?</h3>
                <p style="color: var(--text-sec); line-height: 1.8; margin: 0;">
                    Sistem mendukung format: <strong>PNG, JPG, JPEG, dan DCM</strong>. Ukuran gambar yang direkomendasikan adalah <strong>640x640 - 1280x1280 px</strong> untuk hasil optimal.
                </p>
            </div>

            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h3 style="margin: 0 0 1rem 0; color: var(--primary);">Kenapa analisis gagal?</h3>
                <p style="color: var(--text-sec); line-height: 1.8; margin: 0 0 0.5rem 0;">Beberapa kemungkinan penyebab:</p>
                <ul style="color: var(--text-sec); line-height: 1.8; margin: 0; padding-left: 1.5rem;">
                    <li>Koneksi internet bermasalah</li>
                    <li>Gambar tidak mengandung cedera yang dapat dideteksi</li>
                    <li>Format gambar tidak didukung</li>
                    <li>API quota Roboflow habis (free tier limited)</li>
                    <li>Python atau library belum terinstall</li>
                </ul>
            </div>

            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h3 style="margin: 0 0 1rem 0; color: var(--primary);">Apakah data saya aman?</h3>
                <p style="color: var(--text-sec); line-height: 1.8; margin: 0;">
                    Ya, sistem menggunakan authentication untuk semua operasi sensitif, CSRF protection, dan activity logging. Gambar disimpan secara lokal di server dan hanya dapat diakses oleh user yang login.
                </p>
            </div>

            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid var(--primary);">
                <h3 style="margin: 0 0 1rem 0; color: var(--primary);">Dimana saya bisa belajar lebih lanjut?</h3>
                <p style="color: var(--text-sec); line-height: 1.8; margin: 0;">
                    Dokumentasi lengkap tersedia di folder project:
                </p>
                <ul style="color: var(--text-sec); line-height: 1.8; margin: 0.5rem 0 0 0; padding-left: 1.5rem;">
                    <li><strong>QUICK_START.md</strong> - Panduan cepat</li>
                    <li><strong>INSTALLATION_GUIDE.md</strong> - Setup lengkap</li>
                    <li><strong>FORENSIC_AI_SETUP.md</strong> - Detail fitur AI</li>
                    <li><strong>IMPLEMENTATION_SUMMARY.md</strong> - Technical details</li>
                </ul>
            </div>
        </div>
    </div>
</x-layouts.app>

