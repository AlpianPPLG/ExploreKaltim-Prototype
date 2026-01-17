# 🌴 Explore Kaltim - Tourism Landing Page

Modern, responsive landing page untuk mempromosikan keindahan wisata Kalimantan Timur.

![Explore Kaltim](https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?w=800&q=80)

## ✨ Fitur

- **🎬 Hero Section** - Video background dengan CTA yang menarik
- **🏝️ Destinasi Unggulan** - Grid kartu interaktif dengan filter kategori
- **📸 Interactive Gallery** - Lightbox untuk melihat foto lebih besar
- **💬 Testimonial** - Review dari wisatawan
- **📧 Contact Form** - Form booking yang simple
- **📱 Fully Responsive** - Tampilan optimal di semua device
- **🎨 Modern UI/UX** - Glassmorphism, parallax, smooth animations

## 🏗️ Struktur Project (Modular)

```
/explore-kaltim
├── index.html              # Halaman utama (clean, hanya placeholders)
├── package.json            # Dependencies
├── tailwind.config.js      # Konfigurasi Tailwind
│
├── /src
│   ├── /css
│   │   └── style.css       # Custom CSS & styling
│   │
│   ├── /js
│   │   ├── main.js         # Entry point & logic utama
│   │   ├── animations.js   # AOS & animasi custom
│   │   ├── components.js   # Loader untuk komponen HTML
│   │   └── data.js         # Data destinasi (JSON style)
│   │
│   ├── /components         # Komponen HTML terpisah
│   │   ├── navbar.html     # Navigasi glassmorphism
│   │   ├── hero.html       # Hero section dengan video
│   │   ├── destinations.html
│   │   ├── experience.html
│   │   ├── gallery.html
│   │   ├── testimonials.html
│   │   ├── contact.html
│   │   └── footer.html
│   │
│   └── /assets
│       ├── /img
│       ├── /video
│       └── /icons
│           └── favicon.svg
```

## 🎨 Design System

### Color Palette
| Warna | Hex Code | Penggunaan |
|-------|----------|------------|
| Primary (Emerald) | `#064e3b` | Hutan hujan |
| Secondary (Sky) | `#0ea5e9` | Kejernihan air |
| Accent | `#f5f5f4` | Background netral |

### Typography
- **Heading**: Playfair Display (elegan/luxury)
- **Display**: Montserrat (modern/bold)  
- **Body**: Poppins (readable)

### Visual Effects
- ✅ Glassmorphism pada navbar & cards
- ✅ Parallax scroll
- ✅ AOS (Animate On Scroll)
- ✅ Counter animations
- ✅ Smooth hover effects

## 🚀 Cara Menjalankan

### Development
1. Buka project di XAMPP atau server lokal lainnya
2. Akses `http://localhost/ExploreKaltim`

### Atau dengan Live Server
```bash
# Install VS Code Live Server extension
# Klik kanan index.html -> Open with Live Server
```

## 📁 Sistem Komponen

Website ini menggunakan sistem komponen modular dimana:

1. **index.html** hanya berisi placeholders dan struktur dasar
2. **components.js** memuat semua komponen HTML secara dinamis
3. Setiap section adalah file HTML terpisah di folder `/components`

### Keuntungan:
- ✅ Kode lebih terorganisir
- ✅ Mudah di-maintain
- ✅ Mirip struktur framework modern
- ✅ Tidak ada spaghetti code

### Urutan Loading Komponen:
```javascript
const componentConfig = [
  { id: 'navbar-placeholder', path: './src/components/navbar.html' },
  { id: 'hero-placeholder', path: './src/components/hero.html' },
  { id: 'destinations-placeholder', path: './src/components/destinations.html' },
  { id: 'experience-placeholder', path: './src/components/experience.html' },
  { id: 'gallery-placeholder', path: './src/components/gallery.html' },
  { id: 'testimonials-placeholder', path: './src/components/testimonials.html' },
  { id: 'contact-placeholder', path: './src/components/contact.html' },
  { id: 'footer-placeholder', path: './src/components/footer.html' }
];
```

## 📝 Cara Edit

### Menambah Destinasi Baru
Edit file `src/js/data.js`:
```javascript
const destinations = [
  {
    id: 11,
    name: "Nama Destinasi Baru",
    slug: "slug-destinasi",
    category: "water", // water | forest | culture
    location: "Lokasi, Kalimantan Timur",
    shortDesc: "Deskripsi singkat...",
    image: "url-gambar",
    rating: 4.8,
    reviews: 1000,
    price: "Mulai Rp 500.000",
    duration: "2 Hari",
    highlights: ["Tag1", "Tag2", "Tag3"],
    featured: true
  }
];
```

### Mengubah Tampilan Section
Edit file di folder `/src/components/` sesuai section yang ingin diubah.

## 🔧 Tech Stack

- **HTML5** - Struktur semantik
- **Tailwind CSS** - Styling utility-first
- **Vanilla JavaScript** - Logic tanpa framework
- **AOS** - Scroll animations
- **Unsplash/Mixkit** - Free images & videos

## 📱 Responsive Breakpoints

| Device | Breakpoint |
|--------|------------|
| Mobile | < 640px |
| Tablet | 640px - 1024px |
| Desktop | > 1024px |

## 📄 License

MIT License - Feel free to use for personal or commercial projects.

---

Made with ❤️ for Kalimantan Timur Tourism

