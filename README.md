# AIFI Imaging - AI-Powered Forensic Analysis System

> Sistem analisis forensik medis berbasis AI menggunakan Laravel + Python + Roboflow API

[![Laravel](https://img.shields.io/badge/Laravel-10+-FF2D20?logo=laravel)](https://laravel.com)
[![Python](https://img.shields.io/badge/Python-3.8+-3776AB?logo=python)](https://python.org)
[![OpenCV](https://img.shields.io/badge/OpenCV-4.8+-5C3EE8?logo=opencv)](https://opencv.org)

---

## Features

### AI Forensic Analysis (NEW!)
- **Injury Classification** - Deteksi otomatis: Fracture, Bruise, Burn, Laceration
- **Severity Assessment** - Penilaian tingkat keparahan: Ringan, Sedang, Parah
- **Cause-of-Injury Suggestion** - Prediksi penyebab: Blunt trauma, Sharp force, dll
- **Post-Mortem Detection** - Identifikasi artifact & pola dekomposisi
- **Automatic Annotation** - Bounding box berwarna dengan confidence score

### Image Processing
- 20+ filter & enhancement methods
- Edge detection (Sobel, Canny, Laplacian)
- Morphological operations
- Texture & shape analysis
- Fourier spectrum analysis

### Patient Management
- CRUD patient records
- Image upload & management
- Activity logging & audit trail
- User authentication & authorization

---

## Quick Start

### 1. Requirements
- Laragon (Apache + MySQL)
- PHP 8.1+
- Python 3.8+
- Composer

### 2. Installation
```bash
# Clone or navigate to project
cd C:\laragon\www\final_imaging03

# Install PHP dependencies
composer install

# Install Python dependencies
pip install -r python/requirements.txt

# Setup environment
cp .env.example .env
php artisan key:generate

# Create database 'data_pasien' in phpMyAdmin

# Run migrations
php artisan migrate

# Create storage link
php artisan storage:link

# Start server
php artisan serve
```

### 3. Configuration
Edit `.env`:
```env
PYTHON_PATH="C:\Python\python.exe"
ROBOFLOW_API_KEY="iN6mDa0muAE7Y0Gvp7OM"
ROBOFLOW_MODEL_ID="wrist-fracture-bindi/1"
```

### 4. Usage
1. Register/Login
2. Create Patient
3. Upload Medical Image
4. Select "🔬 AI Forensic Analysis"
5. View Results!

---

## Documentation

- **[QUICK_START.md](QUICK_START.md)** - Panduan cepat 5 menit
- **[INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)** - Setup lengkap & troubleshooting
- **[FORENSIC_AI_SETUP.md](FORENSIC_AI_SETUP.md)** - Detail fitur AI
- **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)** - Technical details
- **[TODO_NEXT_STEPS.md](TODO_NEXT_STEPS.md)** - Langkah selanjutnya

---

## Screenshots

### Forensic Analysis Interface
```
🔬 AI Forensic Analysis
├── Injury Detection with Bounding Boxes
├── Severity Assessment (Color-Coded)
├── Cause-of-Injury Prediction
├── Post-Mortem Feature Detection
└── Comprehensive Forensic Report
```

### Color Coding
- **Green** = Ringan (Mild)
- **Yellow** = Sedang (Moderate)
- **Red** = Parah (Severe)

---

## Tech Stack

**Backend:**
- Laravel 10+
- MySQL/MariaDB

**AI Processing:**
- Python 3.8+
- OpenCV (cv2)
- NumPy
- Requests

**External API:**
- Roboflow Detection API

**Frontend:**
- Blade Templates
- Vanilla JavaScript
- Custom CSS

---

## Project Structure

```
final_imaging03/
├── app/
│   ├── Http/Controllers/
│   │   ├── ForensicAnalysisController.php  ← AI Analysis
│   │   ├── ImageController.php
│   │   └── PatientController.php
│   └── Models/
│       ├── StudyImage.php
│       └── Patient.php
├── python/
│   ├── forensic_analysis.py  ← Main AI Script
│   ├── process.py
│   └── requirements.txt
├── resources/views/
│   └── patients/show.blade.php  ← UI
├── database/migrations/
│   └── 2025_10_12_000001_add_forensic_analysis_to_study_images.php
└── Documentation files (*.md)
```

---

## Security

- Authentication required for all sensitive operations
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- API key stored in .env (not committed)
- Activity logging for audit trail
- File upload validation

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| MySQL error | Start MySQL di Laragon |
| Python not found | Update PYTHON_PATH di .env |
| Module not found | `pip install -r python/requirements.txt` |
| API error | Cek koneksi internet & API key |
| Upload error | Increase upload_max_filesize di php.ini |

Lihat [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) untuk detail lengkap.

---

## Sample Output

```
=== FORENSIC ANALYSIS SUMMARY ===

Total Injuries Detected: 2
Overall Severity: sedang (moderate)

INJURY DETAILS:
1. Fracture (wrist-fracture)
   Confidence: 87.5%
   Severity: sedang
   Probable Cause: blunt trauma (moderate impact)
   Area: 2450.00 px²

POST-MORTEM ANALYSIS:
• No post-mortem features detected
```

---

## Contributing

Contributions are welcome! Please read the contributing guidelines first.

---

## License

This project is licensed under the MIT License.

---

## Credits

- **Laravel Framework** - Web framework
- **Roboflow** - AI model & API
- **OpenCV** - Image processing
- **Python Community** - Libraries & tools

---

## Support

For issues or questions:
1. Check documentation files
2. Review `storage/logs/laravel.log`
3. Contact development team

---

**Built with using Laravel + Python + AI**

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
