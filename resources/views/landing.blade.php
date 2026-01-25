<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEMPAT - Sistem Edukasi Mandiri Pembelajaran Aktif Terpadu</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
        .swiper {
            width: 100%;
            height: 100vh;
        }
        
        .swiper-slide {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: white;
            opacity: 0.5;
        }
        
        .swiper-pagination-bullet-active {
            opacity: 1;
            background: white;
        }
        
        .illustration {
            font-size: 8rem;
            margin-bottom: 2rem;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .slide-content {
            max-width: 500px;
            text-align: center;
        }
        
        @media (max-width: 640px) {
            .illustration {
                font-size: 5rem;
            }
        }
    </style>
</head>
<body class="overflow-hidden">
    <!-- Swiper Container -->
    <div class="swiper onboarding-swiper">
        <div class="swiper-wrapper">
            
            <!-- Slide 1: Welcome -->
            <div class="swiper-slide bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500">
                <div class="slide-content text-white">
                    <div class="illustration">🎓</div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Selamat Datang di SEMPAT</h1>
                    <p class="text-xl md:text-2xl mb-8 opacity-90">
                        Sistem Edukasi Mandiri Pembelajaran Aktif Terpadu
                    </p>
                    <p class="text-lg opacity-80">
                        Platform pembelajaran yang dirancang untuk mendukung pembelajaran mandiri Anda
                    </p>
                    <div class="mt-12 flex justify-center gap-2">
                        <div class="w-2 h-2 bg-white rounded-full"></div>
                        <div class="w-2 h-2 bg-white/50 rounded-full"></div>
                        <div class="w-2 h-2 bg-white/50 rounded-full"></div>
                        <div class="w-2 h-2 bg-white/50 rounded-full"></div>
                    </div>
                    <p class="mt-6 text-sm opacity-75">Swipe untuk melanjutkan →</p>
                </div>
            </div>

            <!-- Slide 2: Facilitated Self-directed Learning -->
            <div class="swiper-slide bg-gradient-to-br from-green-500 via-teal-500 to-cyan-600">
                <div class="slide-content text-white">
                    <div class="illustration">📚</div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Facilitated Self-directed Learning</h2>
                    <p class="text-lg md:text-xl mb-6 opacity-90">
                        Akses ratusan kursus berkualitas dengan struktur pembelajaran yang jelas dan bimbingan instruktur
                    </p>
                    <div class="space-y-4 text-left bg-white/10 backdrop-blur-sm rounded-2xl p-6">
                        <div class="flex items-start gap-3">
                            <div class="text-2xl">✓</div>
                            <div>
                                <h3 class="font-semibold text-lg">Course & Module</h3>
                                <p class="text-sm opacity-80">Materi terstruktur dan sistematis</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="text-2xl">✓</div>
                            <div>
                                <h3 class="font-semibold text-lg">Interactive Lessons</h3>
                                <p class="text-sm opacity-80">Pembelajaran interaktif dengan quiz</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="text-2xl">✓</div>
                            <div>
                                <h3 class="font-semibold text-lg">Progress Tracking</h3>
                                <p class="text-sm opacity-80">Pantau perkembangan belajar Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3: Self pace Learning -->
            <div class="swiper-slide bg-gradient-to-br from-orange-500 via-red-500 to-pink-600">
                <div class="slide-content text-white">
                    <div class="illustration">📖</div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Self pace Learning</h2>
                    <p class="text-lg md:text-xl mb-6 opacity-90">
                        Belajar dengan kecepatan Anda sendiri - Perkaya pengetahuan dengan artikel, jurnal, dan goals pribadi
                    </p>
                    <div class="space-y-4 text-left bg-white/10 backdrop-blur-sm rounded-2xl p-6">
                        <div class="flex items-start gap-3">
                            <div class="text-2xl">📰</div>
                            <div>
                                <h3 class="font-semibold text-lg">Articles & Resources</h3>
                                <p class="text-sm opacity-80">Artikel edukatif dari berbagai topik</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="text-2xl">🎯</div>
                            <div>
                                <h3 class="font-semibold text-lg">Learning Goals</h3>
                                <p class="text-sm opacity-80">Tetapkan dan capai target belajar Anda</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="text-2xl">📓</div>
                            <div>
                                <h3 class="font-semibold text-lg">Learning Journal</h3>
                                <p class="text-sm opacity-80">Catat refleksi pembelajaran Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4: Get Started -->
            <div class="swiper-slide bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-700">
                <div class="slide-content text-white">
                    <div class="illustration">🚀</div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Siap Memulai?</h2>
                    <p class="text-lg md:text-xl mb-8 opacity-90">
                        Mulai perjalanan pembelajaran Anda bersama SEMPAT
                    </p>
                    
                    <div class="space-y-4 w-full max-w-sm mx-auto">
                        <a href="{{ route('register') }}" 
                           class="block w-full bg-white text-purple-600 px-8 py-4 rounded-full text-lg font-bold text-center hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl">
                            ✨ Daftar Sekarang
                        </a>
                        
                        <a href="{{ route('login') }}" 
                           class="block w-full bg-transparent border-2 border-white text-white px-8 py-4 rounded-full text-lg font-bold text-center hover:bg-white/10 transition-all transform hover:scale-105">
                            🔐 Sudah Punya Akun? Masuk
                        </a>
                    </div>

                    <div class="mt-16 space-y-6">
                        <div class="flex items-center justify-center gap-3 text-lg">
                            <div class="text-2xl">✓</div>
                            <span>Gratis selamanya</span>
                        </div>
                        <div class="flex items-center justify-center gap-3 text-lg">
                            <div class="text-2xl">✓</div>
                            <span>Akses tanpa batas</span>
                        </div>
                        <div class="flex items-center justify-center gap-3 text-lg">
                            <div class="text-2xl">✓</div>
                            <span>Belajar kapan saja</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <!-- Pagination -->
        <div class="swiper-pagination"></div>
    </div>

    <!-- Skip Button (hanya muncul di slide 1-3) -->
    <button id="skipBtn" class="fixed top-6 right-6 bg-white/20 backdrop-blur-sm text-white px-6 py-2 rounded-full font-semibold hover:bg-white/30 transition-all z-50">
        Skip
    </button>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <script>
        // Initialize Swiper
        const swiper = new Swiper('.onboarding-swiper', {
            direction: 'horizontal',
            loop: false,
            speed: 600,
            effect: 'slide',
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            keyboard: {
                enabled: true,
            },
            mousewheel: {
                enabled: false,
            },
            on: {
                slideChange: function () {
                    // Hide skip button on last slide
                    const skipBtn = document.getElementById('skipBtn');
                    if (this.activeIndex === 3) {
                        skipBtn.style.display = 'none';
                    } else {
                        skipBtn.style.display = 'block';
                    }
                }
            }
        });

        // Skip button functionality
        document.getElementById('skipBtn').addEventListener('click', function() {
            swiper.slideTo(3); // Go to last slide
        });

        // Touch gestures for better mobile experience
        let touchStartX = 0;
        let touchEndX = 0;

        document.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        });

        document.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            if (touchEndX < touchStartX - 50) {
                swiper.slideNext();
            }
            if (touchEndX > touchStartX + 50) {
                swiper.slidePrev();
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                swiper.slideNext();
            }
            if (e.key === 'ArrowLeft') {
                swiper.slidePrev();
            }
        });
    </script>
</body>
</html>
