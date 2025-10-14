<x-layouts.app :title="'Home'">
    <style>
        .hero-fullscreen {
            position: relative;
            height: 85vh;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.75) 100%), 
                        url('{{ asset('latar_belakang.png') }}') center/cover no-repeat;
            overflow: hidden;
            margin: -1.5rem -1rem 2rem -1rem;
            border-radius: 0;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .hero-content {
            text-align: center;
            color: white;
            z-index: 10;
            animation: fadeInUp 1s ease-out;
            max-width: 1200px;
            padding: 0 2rem;
        }
        
        .hero-title {
            font-size: 4.5rem;
            font-weight: 900;
            line-height: 1.2;
            margin: 0 0 2rem 0;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            letter-spacing: -1px;
        }
        
        .hero-title .highlight {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
            animation: scaleIn 1.2s ease-out 0.3s backwards;
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 500;
            color: #e2e8f0;
            margin: 0 0 3rem 0;
            line-height: 1.8;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 1s ease-out 0.2s backwards;
        }
        
        .hero-cta {
            display: inline-flex;
            gap: 1.5rem;
            animation: fadeInUp 1s ease-out 0.4s backwards;
        }
        
        .cta-button {
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .cta-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .cta-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }
        
        .cta-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid white;
            backdrop-filter: blur(10px);
        }
        
        .cta-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-subtitle {
                font-size: 1.1rem;
            }
            .hero-cta {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>

    <div class="hero-fullscreen">
        <div class="hero-content">
            <h1 class="hero-title">
                Build <span class="highlight">any</span> type of medical imaging & forensic analysis.
            </h1>
            <p class="hero-subtitle">
                In today's fast-paced tech world, every pixel counts. Our platform brings AI to life by turning medical images into smarter insights — faster, sharper, and cooler than ever.
            </p>
            <div class="hero-cta">
                <a href="{{ route('patients.index') }}" class="cta-button cta-primary">Get Started →</a>
                <a href="{{ route('about') }}" class="cta-button cta-secondary">Learn More</a>
            </div>
        </div>
    </div>

    <!-- AI Forensic Analysis Feature Highlight -->
    <div style="max-width:1000px; margin:4rem auto 3rem; padding: 3rem 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 24px; box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);">
        <div style="text-align: center; color: white;">
            <h2 style="margin: 0 0 1.5rem 0; font-size: 2.5rem; font-weight: 800;">AI-Powered Forensic Analysis</h2>
            <p style="font-size: 1.2rem; line-height: 1.8; margin-bottom: 2rem; opacity: 0.95; max-width: 700px; margin-left: auto; margin-right: auto;">
                Teknologi AI terbaru untuk analisis forensik medis otomatis. Deteksi cedera, penilaian keparahan, dan prediksi penyebab dengan akurasi tinggi.
            </p>
            <a href="{{ route('patients.index') }}" style="display: inline-block; background: white; color: #667eea; font-weight: 700; text-decoration: none; padding: 1rem 2.5rem; border-radius: 50px; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.2);" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 40px rgba(0,0,0,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.2)'">
                Mulai Analisis →
            </a>
        </div>
    </div>

    <!-- Features Grid -->
    <div style="background: white; padding: 5rem 2rem; margin-top: 4rem;">
        <div style="max-width:1200px; margin: 0 auto;">
            <h2 style="text-align: center; font-size: 2.5rem; font-weight: 800; margin-bottom: 3rem; color: #1e293b;">Cara Menggunakan</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 2rem;">
                <div style="background: white; border-radius: 20px; padding: 2.5rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.05);" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 40px rgba(102, 126, 234, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)'">
                    <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 800;">1</div>
                    <h3 style="margin: 0 0 1rem 0; color: #1e293b; font-size: 1.3rem; font-weight: 700;">Injury Classification</h3>
                    <p style="color: #64748b; line-height: 1.7; margin: 0; font-size: 1rem;">Deteksi otomatis jenis cedera: Fracture, Bruise, Burn, Laceration</p>
                </div>

                <div style="background: white; border-radius: 20px; padding: 2.5rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.05);" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 40px rgba(102, 126, 234, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)'">
                    <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 800;">2</div>
                    <h3 style="margin: 0 0 1rem 0; color: #1e293b; font-size: 1.3rem; font-weight: 700;">Severity Assessment</h3>
                    <p style="color: #64748b; line-height: 1.7; margin: 0; font-size: 1rem;">Penilaian tingkat keparahan: Ringan, Sedang, Parah</p>
                </div>

                <div style="background: white; border-radius: 20px; padding: 2.5rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.05);" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 40px rgba(102, 126, 234, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)'">
                    <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 800;">3</div>
                    <h3 style="margin: 0 0 1rem 0; color: #1e293b; font-size: 1.3rem; font-weight: 700;">Cause Prediction</h3>
                    <p style="color: #64748b; line-height: 1.7; margin: 0; font-size: 1rem;">Prediksi penyebab cedera berdasarkan pola AI</p>
                </div>

                <div style="background: white; border-radius: 20px; padding: 2.5rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.05);" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 40px rgba(102, 126, 234, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)'">
                    <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 800;">4</div>
                    <h3 style="margin: 0 0 1rem 0; color: #1e293b; font-size: 1.3rem; font-weight: 700;">Post-Mortem Detection</h3>
                    <p style="color: #64748b; line-height: 1.7; margin: 0; font-size: 1rem;">Identifikasi artifact dan pola post-mortem</p>
                </div>
            </div>
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
                    <p style="color: var(--text-sec); margin: 0;">Pilih "AI Forensic Analysis" di control panel dan tunggu hasil</p>
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

