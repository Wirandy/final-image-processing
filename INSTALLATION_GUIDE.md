# 📋 Installation Guide - AI Forensic Imaging System

## Prerequisites

1. **Laragon** (sudah terinstall)
2. **PHP 8.1+** 
3. **MySQL/MariaDB**
4. **Python 3.8+**
5. **Composer**

## Step-by-Step Installation

### 1. Start Laragon Services
```
- Buka Laragon
- Klik "Start All"
- Pastikan Apache & MySQL running (hijau)
```

### 2. Install Python Dependencies
```bash
cd C:\laragon\www\final_imaging03\python
pip install -r requirements.txt
```

Atau install manual:
```bash
pip install opencv-python numpy requests
```

### 3. Configure Environment
Buat file `.env` (copy dari `.env.example` jika ada, atau buat baru):

```env
APP_NAME="AIFI Imaging"
APP_ENV=local
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=data_pasien
DB_USERNAME=root
DB_PASSWORD=

# Python Configuration
PYTHON_PATH="C:\Users\lenovo\AppData\Local\Programs\Python\Python313\python.exe"

# Roboflow API Configuration
ROBOFLOW_API_KEY="iN6mDa0muAE7Y0Gvp7OM"
ROBOFLOW_MODEL_ID="wrist-fracture-bindi/1"
```

**PENTING**: Sesuaikan `PYTHON_PATH` dengan lokasi Python Anda!

Cek lokasi Python:
```bash
where python
```

### 4. Generate Application Key (jika belum ada)
```bash
php artisan key:generate
```

### 5. Create Database
Buka phpMyAdmin atau MySQL client:
```sql
CREATE DATABASE data_pasien CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Run Migrations
```bash
php artisan migrate
```

### 7. Create Storage Link
```bash
php artisan storage:link
```

### 8. Set Permissions (jika di Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
```

## Testing Installation

### 1. Test Laravel
```bash
php artisan serve
```
Buka: http://localhost:8000

### 2. Test Python Script
```bash
cd python
python forensic_analysis.py
```
Seharusnya muncul error usage (normal, artinya script berjalan)

### 3. Test Roboflow API
Buat file test sederhana atau gunakan Postman:
```bash
curl -X POST "https://detect.roboflow.com/wrist-fracture-bindi/1?api_key=iN6mDa0muAE7Y0Gvp7OM" \
  -F file=@path/to/test/image.jpg
```

## Usage Flow

1. **Register/Login** → Buat akun atau login
2. **Create Patient** → Tambah data pasien
3. **Upload Image** → Upload gambar X-ray/medical image
4. **Run Analysis**:
   - Klik "Preview" pada gambar
   - Pilih "🔬 AI Forensic Analysis"
   - Klik "Forensic AI Analysis"
   - Tunggu 5-15 detik
5. **View Results**:
   - Annotated image dengan bounding box
   - Injury count & severity
   - Detailed forensic summary

## Troubleshooting

### MySQL Connection Error
```
✅ Pastikan MySQL running di Laragon
✅ Cek DB_DATABASE ada di phpMyAdmin
✅ Cek DB_USERNAME & DB_PASSWORD benar
```

### Python Not Found
```
✅ Install Python dari python.org
✅ Update PYTHON_PATH di .env
✅ Test: python --version
```

### Module Not Found (cv2, numpy, requests)
```
✅ pip install opencv-python numpy requests
✅ Atau: pip install -r python/requirements.txt
```

### Storage Permission Error
```
✅ php artisan storage:link
✅ Pastikan folder storage/ writable
```

### Roboflow API Error
```
✅ Cek koneksi internet
✅ Verifikasi API key valid
✅ Cek quota API (free tier limited)
```

### Image Upload Error
```
✅ Cek max upload size di php.ini
✅ upload_max_filesize = 20M
✅ post_max_size = 20M
```

## File Structure

```
final_imaging03/
├── app/
│   ├── Http/Controllers/
│   │   ├── ForensicAnalysisController.php  ← AI Analysis Controller
│   │   ├── ImageController.php
│   │   └── PatientController.php
│   ├── Models/
│   │   └── StudyImage.php  ← Updated with forensic fields
│   └── Services/
│       └── ForensicAnalysisService.php  ← (Optional, not used)
├── database/migrations/
│   └── 2025_10_12_000001_add_forensic_analysis_to_study_images.php
├── python/
│   ├── forensic_analysis.py  ← Main AI script
│   ├── process.py  ← Image processing
│   └── requirements.txt
├── resources/views/
│   └── patients/show.blade.php  ← Updated with AI features
├── routes/web.php  ← Updated routes
├── config/app.php  ← Roboflow config
├── FORENSIC_AI_SETUP.md
└── INSTALLATION_GUIDE.md  ← This file
```

## Features Implemented

✅ **Injury Classification** - Fracture, Bruise, Burn, Laceration  
✅ **Severity Assessment** - Ringan, Sedang, Parah  
✅ **Cause-of-Injury Suggestion** - Blunt trauma, Sharp force, etc.  
✅ **Post-Mortem Detection** - Artifacts, decomposition patterns  
✅ **Automatic Annotation** - Color-coded bounding boxes  
✅ **Detailed Summary** - Comprehensive forensic report  

## API Credits

- **Roboflow API**: https://roboflow.com
- **Model**: wrist-fracture-bindi/1
- **Free Tier**: Limited requests per month

## Support

Jika ada masalah:
1. Cek INSTALLATION_GUIDE.md (this file)
2. Cek FORENSIC_AI_SETUP.md untuk detail fitur
3. Cek error log: `storage/logs/laravel.log`
4. Hubungi tim development

---

**Happy Coding! 🚀**
