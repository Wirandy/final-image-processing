<x-layouts.app :title="'About Us'">
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .team-card {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.6s ease-out backwards;
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .team-card:nth-child(1) { animation-delay: 0.1s; }
        .team-card:nth-child(2) { animation-delay: 0.2s; }
        .team-card:nth-child(3) { animation-delay: 0.3s; }
        .team-card:nth-child(4) { animation-delay: 0.4s; }

        .team-card:hover {
            transform: translateY(-20px) rotateX(5deg) rotateY(-5deg) scale(1.05);
            box-shadow: 0 30px 60px rgba(102, 126, 234, 0.5);
        }

        .team-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.3) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.5s, transform 0.5s;
            transform: scale(0);
        }

        .team-card:hover::before {
            opacity: 1;
            transform: scale(1);
        }

        .team-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(102, 126, 234, 0.1) 0%, 
                rgba(118, 75, 162, 0.1) 50%,
                rgba(52, 211, 153, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.5s;
        }

        .team-card:hover::after {
            opacity: 1;
        }

        .team-photo {
            width: 100%;
            height: 320px;
            object-fit: cover;
            transition: transform 0.5s ease, filter 0.5s ease;
            filter: brightness(0.9) contrast(1.1);
        }

        .team-card:hover .team-photo {
            transform: scale(1.15) translateZ(20px);
            filter: brightness(1) contrast(1.2);
        }

        .team-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem 1.5rem;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.8) 70%, transparent 100%);
            transform: translateY(0);
            transition: all 0.4s ease;
            z-index: 10;
        }

        .team-card:hover .team-info {
            background: linear-gradient(to top, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.8) 70%, transparent 100%);
            padding: 2.5rem 1.5rem;
        }

        .team-name {
            color: white;
            font-size: 1.2rem;
            font-weight: 800;
            margin: 0 0 0.5rem 0;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
            letter-spacing: 0.5px;
        }

        .team-role {
            color: #cbd5e1;
            font-size: 0.95rem;
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 3rem;
            animation: fadeInUp 0.6s ease-out;
        }

        .hero-card {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>

    <style>
        .about-hero {
            position: relative;
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            overflow: hidden;
            margin: -1.5rem -1rem 3rem -1rem;
            padding: 4rem 2rem;
        }
        
        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(102, 126, 234, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(52, 211, 153, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(251, 191, 36, 0.1) 0%, transparent 50%);
            opacity: 0.6;
        }
        
        .medical-icon {
            font-size: 4rem;
            margin-bottom: 2rem;
            filter: drop-shadow(0 0 20px rgba(102, 126, 234, 0.5));
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .stat-card {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            color: #cbd5e1;
            font-size: 1rem;
            margin-top: 0.5rem;
        }
    </style>

    <div class="about-hero">
        <div style="max-width: 1000px; text-align: center; position: relative; z-index: 1; color: white;">
            <div class="medical-icon">🏥</div>
            <h1 style="font-size: 3.5rem; font-weight: 900; margin: 0 0 1.5rem 0; line-height: 1.2;">
                Revolutionizing Medical <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">AI Analysis</span>
            </h1>
            <p style="font-size: 1.3rem; line-height: 1.8; color: #cbd5e1; margin: 0 0 2rem 0; max-width: 800px; margin-left: auto; margin-right: auto;">
                Four survivors of the AI apocalypse, ready to slay any assignment thrown at us by our beloved professors. Fueled by caffeine, curiosity, and a little bit of chaos.
            </p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">AI Filters</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">4</div>
                    <div class="stat-label">Injury Types</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">99%</div>
                    <div class="stat-label">Accuracy</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Available</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Our Team Section -->
    <div style="background: #f8fafc; padding: 5rem 2rem;">
        <div style="max-width:1200px; margin: 0 auto;">
            <h2 style="text-align: center; font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; color: #667eea;">OUR TEAM</h2>
            <p style="text-align: center; font-size: 1.1rem; color: #64748b; margin-bottom: 3rem;">Meet the minds behind the innovation</p>
            
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:2rem; margin-bottom: 3rem;">
                <div class="team-card">
                    <img src="{{ asset('THORIQ.jpeg') }}" alt="Team Member 1" class="team-photo">
                    <div class="team-info">
                        <h4 class="team-name">THORIQ</h4>
                        <p class="team-role">LEADER</p>
                    </div>
                </div>
                <div class="team-card">
                    <img src="{{ asset('WIRANDY.jpeg') }}" alt="Team Member 2" class="team-photo">
                    <div class="team-info">
                        <h4 class="team-name">WIRANDY</h4>
                        <p class="team-role">MEMBER</p>
                    </div>
                </div>
                <div class="team-card">
                    <img src="{{ asset('ARKA.jpeg') }}" alt="Team Member 3" class="team-photo">
                    <div class="team-info">
                        <h4 class="team-name">ARKA</h4>
                        <p class="team-role">MEMBER</p>
                    </div>
                </div>
                <div class="team-card">
                    <img src="{{ asset('ALEX.jpeg') }}" alt="Team Member 4" class="team-photo">
                    <div class="team-info">
                        <h4 class="team-name">ALEX</h4>
                        <p class="team-role">MEMBER</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Full-Screen Story Hero -->
    <div style="position: relative; min-height: 70vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.75) 100%), url('{{ asset('latar_belakang.png') }}') center/cover no-repeat; overflow: hidden; margin: 0 -1rem;">
        <div style="max-width: 1000px; text-align: center; padding: 4rem 2rem; position: relative; z-index: 1;">
            <h2 style="font-size: 3.5rem; font-weight: 900; line-height: 1.3; color: white; margin: 0 0 2rem 0; text-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                Build <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">any</span> type of medical imaging & forensic analysis.
            </h2>
            
            <div style="max-width: 800px; margin: 0 auto;">
                <p style="font-size: 1.2rem; line-height: 1.9; color: #e2e8f0; margin: 0 0 1.5rem 0;">
                    <strong style="color: #fbbf24; font-size: 1.3rem;">Four survivors of the AI apocalypse</strong>, ready to slay any assignment thrown at us by our beloved professors.
                </p>
                <p style="font-size: 1.15rem; line-height: 1.9; color: #cbd5e1; margin: 0 0 1.5rem 0;">
                    Fueled by <span style="color: #fbbf24; font-weight: 700;">caffeine</span>, <span style="color: #60a5fa; font-weight: 700;">curiosity</span>, and a little bit of <span style="color: #f472b6; font-weight: 700;">chaos</span>, we turn complex algorithms into something (hopefully) that actually works.
                </p>
                <p style="font-size: 1.15rem; line-height: 1.9; color: #cbd5e1; margin: 0 0 1.5rem 0;">
                    We're not just building AI — we're <strong style="color: #667eea;">experimenting</strong>, <strong style="color: #34d399;">learning</strong>, and sometimes <strong style="color: #f472b6;">debugging at 3 AM</strong>.
                </p>
                <p style="font-size: 1.3rem; line-height: 1.9; color: #f8fafc; margin: 0; font-weight: 700; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                    Welcome to our journey of turning <span style="color: #fbbf24; font-size: 1.4rem;">sleepless nights into smart innovations</span>.
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>

