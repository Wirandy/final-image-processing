# 🔧 Troubleshooting Upload Image Error

## Kemungkinan Penyebab & Solusi

### 1. ❌ Belum Login
**Error:** "Unauthenticated" atau redirect ke login

**Solusi:**
- Pastikan Anda sudah login
- Route upload memerlukan middleware `auth`
- Klik "Login" di navbar terlebih dahulu

---

### 2. 📁 Folder Storage Tidak Ada
**Error:** "The file could not be stored" atau "Directory not found"

**Solusi:**
```bash
# Buat folder yang dibutuhkan
mkdir storage\app\public\uploads
mkdir storage\app\public\uploads\originals
mkdir storage\app\public\uploads\processed
mkdir storage\app\public\uploads\annotated

# Atau jalankan command ini
php artisan storage:link
```

---

### 3. 🔒 Permission Error (Windows)
**Error:** "Permission denied" atau "Access denied"

**Solusi:**
1. Klik kanan folder `storage` → Properties
2. Tab Security → Edit
3. Pilih user Anda → Check "Full Control"
4. Apply → OK

Atau via command:
```powershell
icacls storage /grant Users:F /T
```

---

### 4. 📦 File Size Terlalu Besar
**Error:** "The file exceeds the maximum upload size"

**Solusi:**
Edit `php.ini` (di Laragon: Menu → PHP → php.ini):
```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
```

Restart Apache setelah edit.

---

### 5. 🔗 Storage Link Tidak Ada
**Error:** "File not found" saat preview image

**Solusi:**
```bash
php artisan storage:link
```

Cek apakah folder `public/storage` ada dan merupakan symlink ke `storage/app/public`

---

### 6. 🗄️ Database Error
**Error:** "SQLSTATE[HY000]" atau "Table not found"

**Solusi:**
```bash
# Pastikan migration sudah dijalankan
php artisan migrate

# Cek database
php artisan migrate:status
```

---

### 7. 📝 Validation Error
**Error:** "The image field is required" atau "Invalid file type"

**Solusi:**
- Pastikan file yang diupload adalah gambar (PNG, JPG, JPEG, DCM)
- Ukuran file < 20MB
- File tidak corrupt

Format yang didukung:
- ✅ PNG
- ✅ JPG/JPEG
- ✅ DCM (DICOM)
- ❌ GIF, BMP, TIFF (tidak didukung)

---

### 8. 🌐 CSRF Token Error
**Error:** "419 | Page Expired" atau "CSRF token mismatch"

**Solusi:**
- Refresh halaman (Ctrl + F5)
- Clear browser cache
- Pastikan `@csrf` ada di form

---

### 9. 🔧 .env Configuration
**Error:** Various errors

**Solusi:**
Pastikan `.env` sudah dikonfigurasi dengan benar:
```env
APP_KEY=base64:...  # Harus ada
DB_CONNECTION=mysql
DB_DATABASE=data_pasien
FILESYSTEM_DISK=public
```

Generate key jika belum:
```bash
php artisan key:generate
```

---

## 🧪 Testing Upload

### Test Manual:
1. Login ke aplikasi
2. Buka Patients → Pilih/buat patient
3. Upload gambar test (PNG/JPG kecil dulu, < 1MB)
4. Lihat apakah muncul di list images

### Test via Command:
```bash
# Cek permissions
php artisan storage:link

# Cek folder structure
dir storage\app\public\uploads

# Cek logs
type storage\logs\laravel.log
```

---

## 📊 Debug Checklist

Cek satu per satu:

- [ ] Sudah login?
- [ ] Folder `storage/app/public/uploads/originals` ada?
- [ ] Folder `public/storage` ada (symlink)?
- [ ] File size < 20MB?
- [ ] Format file PNG/JPG/JPEG?
- [ ] Migration sudah dijalankan?
- [ ] Database connection OK?
- [ ] Apache & MySQL running di Laragon?
- [ ] Browser cache sudah di-clear?

---

## 🔍 Cara Lihat Error Detail

### 1. Cek Laravel Log:
```bash
type storage\logs\laravel.log
```

### 2. Enable Debug Mode:
Edit `.env`:
```env
APP_DEBUG=true
```

Refresh halaman, error akan muncul detail di browser.

### 3. Cek Browser Console:
- Buka Developer Tools (F12)
- Tab Console
- Lihat error JavaScript (jika ada)
- Tab Network → Lihat response upload request

---

## 💡 Quick Fix Commands

Jalankan semua command ini untuk fix common issues:

```bash
# 1. Recreate storage link
php artisan storage:link

# 2. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 3. Fix permissions (Windows - run as Admin)
icacls storage /grant Users:F /T

# 4. Check migration
php artisan migrate:status

# 5. Run migration if needed
php artisan migrate
```

---

## 📞 Masih Error?

Jika masih error, berikan informasi berikut:

1. **Error message lengkap** (screenshot atau copy text)
2. **Kapan error terjadi** (saat klik upload / setelah pilih file)
3. **Sudah login atau belum**
4. **File yang diupload** (format, ukuran)
5. **Isi `storage/logs/laravel.log`** (baris terakhir)

Dengan informasi ini saya bisa bantu debug lebih spesifik.

---

## ✅ Jika Berhasil

Setelah upload berhasil, Anda akan melihat:
- ✅ Message "Image uploaded" (hijau)
- ✅ Gambar muncul di list Images
- ✅ Thumbnail preview
- ✅ Button "Preview" dan "Delete"

Lanjutkan dengan:
1. Klik "Preview" untuk melihat gambar
2. Pilih "🔬 AI Forensic Analysis" di control panel
3. Lihat hasil analisis!

---

**Good Luck! 🚀**
