# ⚡ Quick Start - AI Forensic Analysis

## 🚀 Setup Cepat (5 Menit)

### 1. Start Laragon
- Buka Laragon → Klik **Start All**
- Pastikan MySQL & Apache running (hijau)

### 2. Install Python Libraries
```bash
pip install opencv-python numpy requests
```

### 3. Setup Database
```bash
# Di terminal/command prompt:
cd C:\laragon\www\final_imaging03
php artisan migrate
```

Jika error "database not found":
- Buka phpMyAdmin (http://localhost/phpmyadmin)
- Create database: `data_pasien`
- Run lagi: `php artisan migrate`

### 4. Test Aplikasi
```bash
php artisan serve
```
Buka: http://localhost:8000

---

## 🔬 Cara Pakai Forensic AI

### Step-by-Step:

1. **Register** → Buat akun baru
2. **Login** → Masuk ke sistem
3. **Patients** → Klik "Add New Patient"
4. **Upload Image** → Upload X-ray/medical image
5. **Klik Preview** → Pilih gambar yang mau dianalisis
6. **Pilih "🔬 AI Forensic Analysis"** → Di panel FILTER
7. **Klik "Forensic AI Analysis"** → Konfirmasi
8. **Tunggu 5-15 detik** → AI sedang bekerja
9. **Lihat Hasil**:
   - ✅ Annotated image dengan bounding box berwarna
   - ✅ Jumlah cedera terdeteksi
   - ✅ Tingkat keparahan (Ringan/Sedang/Parah)
   - ✅ Penyebab cedera (Blunt trauma, Sharp force, dll)
   - ✅ Analisis post-mortem
   - ✅ Summary lengkap

---

## 🎨 Kode Warna Severity

- 🟢 **Hijau** = Ringan (area < 1000 px²)
- 🟡 **Kuning** = Sedang (area 1000-3000 px²)
- 🔴 **Merah** = Parah (area > 3000 px²)

---

## 📊 Contoh Output

```
=== FORENSIC ANALYSIS SUMMARY ===

Total Injuries Detected: 2
Overall Severity: sedang (moderate)

INJURY DETAILS:
--------------------------------------------------

1. Fracture (wrist-fracture)
   Confidence: 87.5%
   Severity: sedang
   Probable Cause: blunt trauma (moderate impact)
   Area: 2450.00 px²

2. Fracture (wrist-fracture)
   Confidence: 92.3%
   Severity: ringan
   Probable Cause: stress fracture or minor trauma
   Area: 850.00 px²

POST-MORTEM ANALYSIS:
--------------------------------------------------
• No post-mortem features detected
```

---

## ⚙️ Konfigurasi (Optional)

Edit file `.env` untuk custom settings:

```env
# Ganti dengan path Python Anda
PYTHON_PATH="C:\Python\python.exe"

# Ganti dengan API key Anda sendiri
ROBOFLOW_API_KEY="your_api_key_here"
ROBOFLOW_MODEL_ID="your_model_id/1"
```

---

## 🐛 Troubleshooting Cepat

| Problem | Solution |
|---------|----------|
| MySQL error | Start MySQL di Laragon |
| Python not found | Install Python atau update PYTHON_PATH |
| Module not found | `pip install opencv-python numpy requests` |
| API error | Cek koneksi internet |
| Upload error | Cek php.ini: `upload_max_filesize = 20M` |

---

## 📁 File Penting

- **Python AI Script**: `python/forensic_analysis.py`
- **Controller**: `app/Http/Controllers/ForensicAnalysisController.php`
- **View**: `resources/views/patients/show.blade.php`
- **Config**: `config/app.php`
- **Migration**: `database/migrations/2025_10_12_000001_add_forensic_analysis_to_study_images.php`

---

## 📚 Dokumentasi Lengkap

- **INSTALLATION_GUIDE.md** → Setup lengkap
- **FORENSIC_AI_SETUP.md** → Detail fitur AI
- **QUICK_START.md** → Panduan ini

---

## ✨ Fitur yang Sudah Diimplementasi

✅ Injury Classification (Fracture, Bruise, Burn, Laceration)  
✅ Severity Assessment (Ringan, Sedang, Parah)  
✅ Cause-of-Injury Suggestion (Blunt trauma, Sharp force)  
✅ Post-Mortem Feature Detection  
✅ Automatic Color-Coded Annotation  
✅ Comprehensive Forensic Summary  

---

**Selamat Menggunakan! 🎉**
