# Change Log — SMSv3 Admin Panel Modernization

## 2026-05-23

### 1. Design Tokens (`globals.css`)
- Modern color palette dengan primary blue→indigo
- Shadow system lengkap (xs → xl, card, card-hover, glass, sidebar)
- CSS custom properties untuk glass, glass-border, glass-shadow
- Custom keyframes: `float`, `pulse-glow`, `gradient-shift`, `shimmer`, `scale-in`, `slide-up-fade`
- Utility classes: `glass`, `glass-card`, `text-gradient`, `animate-float`, `animate-pulse-glow`, `animate-gradient`, `animate-shimmer`, `animate-scale-in`, `animate-slide-up`
- Scrollbar custom (4px)
- `border-radius: 1rem` global

### 2. Layout (`MainLayout.tsx`)
- **Sidebar**: Glass effect → solid `bg-sidebar`, gradient active state, dot indicator, rounded-xl pada semua elemen, search input untuk filter menu, logo size 9 dengan gradient blue→indigo, shadow-lg pada logo
- **Header**: Glass effect → solid `bg-background`, `h-16`, rounded-xl pada tombol, search bar dengan kbd shortcut `⌘K`, section accent indicator
- **Breadcrumb**: Label Indonesia proper (Manajemen Siswa, Penilaian, dll)
- **Responsive**: Mobile off-canvas sidebar, overlay `bg-black/60`, backdrop-blur dihapus
- **Animasi**: Framer Motion page transitions

### 3. UI Components

#### Card.tsx
- rounded-2xl
- `glass` prop: solid background via `.glass` class
- Accent bar: gradient fade
- Hover: `translateY(-1px)`, `shadow-lg`

#### Button.tsx
- `pill` prop (default true): `rounded-full`
- Gradient primary: `from-blue-600 to-indigo-600`
- Gradient danger: `from-red-600 to-rose-600`
- Shadow dengan color tint
- whileTap: scale 0.97

#### Badge.tsx
- `pill` prop (default true): `rounded-full`
- `dot` prop (default true): colored indicator dot
- `shadow-sm`
- Warna lebih soft, border transparan

#### Input.tsx / Select.tsx
- rounded-xl
- `h-10` (lebih tinggi)
- Background solid `bg-background` / `bg-card`
- Focus: ring dengan shadow
- Disabled: `bg-muted`

#### PageHeader.tsx
- `gradient` prop untuk `text-gradient` title
- Breadcrumb lebih bold (font-semibold)
- Accent bar: linear gradient fade

#### StatCard.tsx
- Glass → solid `border border-border/50 bg-card`
- Icon background: gradient 135deg
- Trend badge: pill dengan warna solid
- Hover: translateY(-3px)

### 4. Dashboard (`dashboard/page.tsx`)

#### WelcomeBanner
- Gradient dengan decorative pattern overlay
- Avatar: solid `bg-white/20` tanpa backdrop-blur
- Online badge: solid tanpa backdrop-blur
- Glow elements (`blur-3xl`): dihapus

#### SectionCard
- Glass → solid Card component
- border-border/50

#### Charts
- StackedBar: rounded corners lebih besar (3px)
- Legend: gap lebih besar, dot lebih kecil

#### Quick Actions
- Label "Menu Cepat:"
- Rounded-lg, border border-border/60

#### Academic Year Selector
- Rounded-lg, border border-border/60

#### Student Trend
- Input solid `bg-background`
- Tombol gradient blue→indigo

### 5. Login Page (`login/page.tsx`)
- Tombol submit: `rounded-full` dengan gradient from-blue-600 to-indigo-600
- Logo: floating animation, rounded-2xl, gradient blue→indigo
- Error toast: solid background, border solid

### 6. Transparency Removal (semua komponen)

| Komponen | Sebelum | Sesudah |
|----------|---------|---------|
| Sidebar | `backdrop-blur-xl` | solid `bg-sidebar` |
| Header | `backdrop-blur-xl bg-background/60` | solid `bg-background` |
| Mobile overlay | `bg-black/30 backdrop-blur-sm` | `bg-black/60` |
| Search bar | `bg-muted/30` → `bg-background/60` | `bg-muted` → `bg-background` |
| Kbd shortcut | `bg-background/50` | `bg-background` |
| Input / Select | `bg-background/50` | `bg-background` / `bg-card` |
| Disabled state | `bg-muted/30` | `bg-muted` |
| Card .glass | backdrop-filter blur | solid |
| StatCard | glass class | solid `border bg-card` |
| SectionCard | border-glass-border | border-border/50 |
| Skeleton | `bg-muted/50` | `bg-muted` |
| DataTable head | `bg-muted/30` / `bg-muted/20` | `bg-muted` |
| EmptyState | `bg-muted/30` | `bg-muted` |
| Modal overlay | `bg-black/40` | `bg-black/60` |
| ConfirmDialog overlay | `bg-black/40` | `bg-black/60` |
| WelcomeBanner avatar | `backdrop-blur-md bg-white/15` | `bg-white/20` |
| WelcomeBanner online badge | `backdrop-blur-sm bg-white/10` | `bg-white/15` |
| WelcomeBanner glow | `bg-white/5 blur-3xl` | removed |
| Parent avatar | `bg-white/20` | `bg-blue-500 text-white` |

### 7. Verifikasi
- TypeScript: ✅ 0 errors
- ESLint: ✅ 0 new issues from changed files
- Tests: ✅ 85 passed (18 test files)

### Files Changed
```
src/app/globals.css
src/components/layout/MainLayout.tsx
src/components/ui/Card.tsx
src/components/ui/Button.tsx
src/components/ui/Badge.tsx
src/components/ui/Input.tsx
src/components/ui/PageHeader.tsx
src/components/ui/DataTable.tsx
src/components/ui/Modal.tsx
src/components/ui/ConfirmDialog.tsx
src/components/ui/Skeleton.tsx
src/components/ui/EmptyState.tsx
src/components/widgets/StatCard.tsx
src/app/dashboard/page.tsx
src/app/login/page.tsx
```
