# Roadmap – School Management System v4

## Phase 1 – Core Stabilization (Done)
- Zenith‑style admin panel UI (sidebar, navbar, cards, tables)
- All modules (siswa, guru, kelas, kehadiran, jadwal, nilai, pembayaran, laporan, dokumen)
- Bug fixes: route group, notification class, JS toggle
- Full test suite passing (120 tests)
- CI pipeline (GitHub Actions) added
- Auth middleware applied to admin routes
- Dark‑mode support enabled via Tailwind

## Phase 2 – Feature Enhancements (Next 1‑3 months)
- Role‑Based Access Control (RBAC) using spatie/laravel-permission
- Profile page & user settings (avatar, theme preference)
- Export/Import CSV/Excel for each module (Laravel‑Excel)
- Notifications via database + broadcasting (WebSocket)
- Real‑time charts on dashboard (Chart.js via Echo)
- Multi‑language support (ID/EN) with LangJS

## Phase 3 – Advanced Integrations (3‑6 months)
- Payment gateway (Midtrans/Xendit) for pembayaran module
- Biometric / RFID attendance integration
- Google Calendar sync for jadwal
- LDAP/Active Directory login option
- REST API documentation (Scribe) fully covered
- Docker‑compose setup for easy local/dev deployment

## Phase 4 – Scaling & Innovation (6‑12 months)
- PWA (Service Worker + Manifest) for offline capability
- React Native mobile app (student/parent portal)
- AI‑assisted grade prediction & dropout risk
- Virtual Classroom (WebRTC) integration
- Multi‑school tenancy (centralized admin)
- Monetization: freemium & white‑label offering

## Phase 5 – Production & Maintenance (Ongoing)
- Monitoring: Laravel Telescope + Sentry
- Log rotation & backup strategy
- Regular security audits (OWASP Top 10)
- Performance tuning: DB indexing, query caching, Redis
- Quarterly dependency updates & vulnerability scans

---
*Semua rencana dapat disesuaikan berdasarkan umpan balik pengguna dan kebutuhan operasional.* 
