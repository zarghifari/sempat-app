# LMS SEMPAT - Learning Management System

![Laravel](https://img.shields.io/badge/Laravel-12.46-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.4-blue?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?logo=mysql)

Sistem pembelajaran digital modern untuk SMA/SMK Indonesia, mendukung **Self-Directed Learning** (SDL) dengan dua mode utama: Facilitated (FSDL) & Self-Paced (SPSDL).

---

## 🚀 Fitur Utama

- **Dual Learning Modes**
  - Facilitated Self-Directed Learning (FSDL): Kursus terstruktur, progress tracking, prerequisite, quiz, analytics
  - Self-Paced Self-Directed Learning (SPSDL): Artikel mandiri, rekomendasi, bookmark, catatan
- **Document Import**: Import & transformasi .docx/.doc ke HTML, media extraction, queue processing
- **Learning Goals & Journal**: Target belajar harian, progress dari lessons + articles, refleksi, auto tracking
- **Quiz & Assessment**: MCQ, Essay, auto/manual grading, feedback, analytics
- **Student Progress Monitoring**: Dashboard real-time, leaderboard, detail per siswa, auto-refresh
- **Teacher/Admin Dashboard**: Action items, quick stats, analytics mini, grading, quick access
- **Performance Optimization**: Redis cache, batch queries, eager loading, API terpisah, auto cache invalidation
- **Role-based Access**: Admin, Teacher, Student
- **Mobile-First Design**: Responsive, siap untuk pengembangan mobile app

---

## ⚡ Quick Start

### 📋 Panduan Setup

**Pilih sesuai kebutuhan Anda:**

- 🚀 **[Quick Start (5 Menit)](QUICKSTART.md)** - Setup cepat untuk mulai development
- 🖥️ **[Setup XAMPP Lengkap](XAMPP-SETUP.md)** - Panduan detail untuk XAMPP Windows
- 🌐 **[Production Setup](PRODUCTION-SETUP.md)** - Deploy ke server production
- 📦 **[Deployment Guide](DEPLOYMENT-GUIDE.md)** - Panduan deployment existing
- 🚀 **[Deploy Checklist](DEPLOY-CHECKLIST.md)** - Checklist deployment production
- 💻 **[WinSCP Deploy Guide](WINSCP-DEPLOY-GUIDE.md)** - Deploy dengan WinSCP (Windows)
- ⚡ **[Deploy with ZIP](DEPLOY-WITH-ZIP.md)** - Deploy super cepat dengan ZIP (RECOMMENDED!)

### Prasyarat
- PHP 8.2+ (disarankan 8.4+)
- Composer
- MySQL 8.0+
- Node.js 18+ & NPM
- XAMPP (Windows) atau web server lain

### Instalasi Singkat
```bash
# 1. Install dependencies
composer install
npm install

# 2. Switch ke environment local (opsional, jika file .env.local tersedia)
# Windows: switch-env.bat
# PowerShell: .\switch-env.ps1
# Linux/Mac: ./switch-env.sh

# 3. Setup database
php artisan key:generate
php artisan migrate
php artisan storage:link

# 4. Build assets
npm run build

# 5. Akses aplikasi
# Dengan Virtual Host: http://sempat.test
# Tanpa Virtual Host: http://localhost/sempat-app/public
```

### Environment Switcher

Gunakan script helper untuk beralih antara local dan production:

**Windows (CMD):**
```bash
switch-env.bat
```

**Windows (PowerShell):**
```bash
.\switch-env.ps1
```

**Linux/Mac:**
```bash
./switch-env.sh
```

---

## 📖 Dokumentasi Teknis

- [Dokumentasi Utama & Index](docs/README.md)
- [Arsitektur Sistem](docs/01-System-Architecture-Overview.md)
- [Fitur & Modul](docs/04-Features-and-Modules.md)
- [API & Optimasi](docs/03-API-Design-and-Optimization.md)
- [Dashboard Guru/Admin](docs/TEACHER-ADMIN-DASHBOARD.md)
- [Quiz Implementation](docs/QUIZ-IMPLEMENTATION.md)
- [Role Structure Update](docs/ROLE-STRUCTURE-UPDATE.md)
- [Development Roadmap](docs/07-Development-Roadmap.md)

---

## 🏆 Highlight Fitur & Optimasi Terbaru

- **Learning Goals Redesign**: Target harian otomatis, progress dari lessons + articles (single source of truth)
- **Student Progress Monitoring**: Real-time, auto-refresh, leaderboard, detail mingguan
- **Teacher Dashboard**: Action items, quick stats, mini analytics, grading, responsive
- **Redis Cache**: 200x lebih cepat, batch queries, auto invalidation
- **Eager Loading**: Eliminasi N+1 queries, API super cepat
- **Document Import**: .docx/.doc ke HTML, media extraction, queue
- **Quiz System**: MCQ, Essay, auto/manual grading, feedback
- **Role-based Access**: Admin, Teacher, Student

---

## 🛠️ Kontribusi & Pengembangan

Lihat [docs/README.md](docs/README.md) untuk panduan teknis lengkap, arsitektur, dan pengembangan fitur lanjutan.
   cd sempat-app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database in `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sempat_lms
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Create database**
   - See [SETUP-WINDOWS.md](SETUP-WINDOWS.md) for detailed instructions

7. **Run migrations**
   ```bash
   php artisan migrate
   ```

8. **Build assets**
   ```bash
   npm run dev
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to see your application.

## 📖 Documentation

Comprehensive documentation is available in the `docs/` folder:

1. **[System Architecture Overview](docs/01-System-Architecture-Overview.md)** - System design and architecture
2. **[Database Design](docs/02-Database-Design.md)** - Complete database schema
3. **[API Design & Optimization](docs/03-API-Design-and-Optimization.md)** - RESTful API documentation
4. **[Features & Modules](docs/04-Features-and-Modules.md)** - Detailed feature specifications
5. **[Technical Implementation](docs/05-Technical-Implementation-Strategy.md)** - Implementation guidelines
6. **[Security & Performance](docs/06-Security-and-Performance.md)** - Security and optimization strategies
7. **[Development Roadmap](docs/07-Development-Roadmap.md)** - 10-month development plan

Also see:
- **[SETUP-WINDOWS.md](SETUP-WINDOWS.md)** - Windows/XAMPP setup guide
- **[docs/README.md](docs/README.md)** - Documentation index

## 🏗️ Technology Stack

**Backend:**
- Laravel 12.x
- MySQL 8.0+
- Redis (for caching and queues)
- PHP 8.4

**Frontend:**
- Laravel Blade + Alpine.js/Livewire
- Tailwind CSS
- Vite
- Optional: Vue.js 3 for SPA features

**Infrastructure:**
- Nginx (production)
- Supervisor (queue workers)
- Laravel Scheduler (cron jobs)

## 🔒 Security

This application implements multiple security layers:
- Laravel Sanctum for API authentication
- Role-Based Access Control (RBAC)
- Input validation and sanitization
- CSRF and XSS protection
- SQL injection prevention
- File upload security with virus scanning
- Rate limiting

See [Security & Performance](docs/06-Security-and-Performance.md) for details.

## ⚡ Performance Optimization

- Multi-layer caching (Application, HTTP, Database, CDN)
- Database query optimization with proper indexing
- Eager loading to prevent N+1 queries
- Asset optimization (minification, compression)
- Image optimization and lazy loading
- Queue-based processing for heavy tasks

See [API Design & Optimization](docs/03-API-Design-and-Optimization.md) for details.

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

## 📋 Development Roadmap

The project follows a **10-month development roadmap**:

- **Phase 1 (Month 1-2)**: Foundation - User management, basic modules
- **Phase 2 (Month 3-4)**: Learning Content - FSDL, SPSDL, Document import
- **Phase 3 (Month 5-6)**: Assessments & Analytics
- **Phase 4 (Month 7-8)**: SDL Features & Communication
- **Phase 5 (Month 9)**: Admin & Reporting
- **Phase 6 (Month 10)**: Polish & Launch
- **Phase 7 (Month 11-12)**: Post-Launch optimization

See [Development Roadmap](docs/07-Development-Roadmap.md) for detailed timeline.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please follow the coding standards and best practices outlined in [Development Roadmap](docs/07-Development-Roadmap.md).

## 📝 License

This project is licensed under the [MIT License](LICENSE).

## 👥 Team

- **System Architect**: [Your Name]
- **Backend Developer**: [Your Name]
- **Frontend Developer**: [Your Name]
- **UI/UX Designer**: [Your Name]

## 📧 Contact

For questions or support, please contact:
- Email: support@sempat-lms.example.com
- Documentation: [docs/README.md](docs/README.md)

## 🙏 Acknowledgments

- Laravel Framework
- PHPOffice/PHPWord for document processing
- The open-source community

---

**Built with ❤️ for Indonesian students**
