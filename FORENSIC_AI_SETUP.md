# 🔬 AI-Powered Forensic Analysis Setup

## Overview
Sistem analisis forensik berbasis AI yang menggunakan Roboflow API untuk mendeteksi dan mengklasifikasi cedera pada gambar medis.

## Fitur Utama

### 1. **Injury Classification** 
- Deteksi otomatis jenis cedera: Fracture, Bruise, Burn, Laceration
- Menggunakan CNN model dari Roboflow

### 2. **Severity Assessment**
- Penilaian tingkat keparahan: Ringan, Sedang, Parah
- Berdasarkan area dan karakteristik cedera

### 3. **Cause-of-Injury Suggestion**
- Prediksi penyebab cedera: Blunt trauma, Sharp force, Minor trauma
- Analisis berdasarkan pola deteksi

### 4. **Post-Mortem Feature Detection**
- Identifikasi artifact post-mortem
- Deteksi pola dekomposisi
- Analisis objek asing

### 5. **Automatic Annotation**
- Overlay bounding box dengan kode warna:
  - 🟢 Hijau = Ringan
  - 🟡 Kuning = Sedang  
  - 🔴 Merah = Parah

## Installation

### 1. Install Python Dependencies
```bash
cd python
pip install -r requirements.txt
```

### 2. Configure Environment Variables
Tambahkan ke file `.env`:
```env
# Python Path
PYTHON_PATH="C:\Users\lenovo\AppData\Local\Programs\Python\Python313\python.exe"

# Roboflow API Configuration
ROBOFLOW_API_KEY="iN6mDa0muAE7Y0Gvp7OM"
ROBOFLOW_MODEL_ID="wrist-fracture-bindi/1"
```

### 3. Run Database Migration
```bash
php artisan migrate
```

## Usage

### Cara Menggunakan Fitur Forensic Analysis:

1. **Login** ke sistem
2. Buka halaman **Patients**
3. Pilih atau buat **Patient** baru
4. **Upload** gambar medis (X-ray, CT scan, dll)
5. Klik tombol **Preview** pada gambar
6. Di panel **FILTER**, pilih kategori **🔬 AI Forensic Analysis**
7. Klik tombol **Forensic AI Analysis**
8. Konfirmasi analisis
9. Tunggu proses selesai (5-15 detik)
10. Lihat hasil:
    - **Annotated Image** dengan bounding box berwarna
    - **Injury Count** (jumlah cedera)
    - **Severity Level** (tingkat keparahan)
    - **Detailed Summary** (ringkasan lengkap)

## API Configuration

### Menggunakan Model Roboflow Sendiri:

1. Daftar di [Roboflow](https://roboflow.com)
2. Train model atau gunakan model publik
3. Dapatkan **API Key** dan **Model ID**
4. Update di `.env`:
```env
ROBOFLOW_API_KEY="your_api_key_here"
ROBOFLOW_MODEL_ID="your_model_id/version"
```

## Output Format

### Forensic Analysis Result:
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
   Bounding Box: [120, 85, 245, 180]

2. Fracture (wrist-fracture)
   Confidence: 92.3%
   Severity: ringan
   Probable Cause: stress fracture or minor trauma
   Area: 850.00 px²
   Bounding Box: [300, 150, 380, 210]

==================================================
POST-MORTEM ANALYSIS:
--------------------------------------------------
• No post-mortem features detected

Confidence Level: high
```

## Technical Details

### Python Script: `python/forensic_analysis.py`
- Memanggil Roboflow API
- Memproses predictions
- Menghitung severity
- Menggambar annotations
- Generate summary

### PHP Controller: `ForensicAnalysisController.php`
- Trigger Python script
- Simpan hasil ke database
- Handle errors
- Log activity

### Database Fields:
- `annotated_path` - Path gambar dengan annotation
- `forensic_analysis` - JSON data lengkap
- `forensic_summary` - Text summary
- `injury_count` - Jumlah cedera
- `severity_level` - Tingkat keparahan

## Troubleshooting

### Error: "Python not found"
- Pastikan `PYTHON_PATH` di `.env` benar
- Test: `python --version` di terminal

### Error: "Module not found"
- Install dependencies: `pip install -r python/requirements.txt`

### Error: "API request failed"
- Cek koneksi internet
- Verifikasi `ROBOFLOW_API_KEY` valid
- Cek quota API Roboflow

### Error: "No predictions found"
- Gambar mungkin tidak mengandung cedera
- Coba gambar lain atau model berbeda

## Notes

- **API Rate Limit**: Roboflow free tier memiliki limit request
- **Processing Time**: 5-15 detik per gambar
- **Supported Formats**: PNG, JPG, JPEG
- **Recommended Size**: 640x640 - 1280x1280 px

## Support

Untuk pertanyaan atau issue, silakan hubungi tim development.
