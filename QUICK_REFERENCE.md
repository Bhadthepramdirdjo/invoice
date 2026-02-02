# 🎨 Quick Reference - TailwindCSS Classes

Panduan cepat untuk class-class yang tersedia di Invoice App.

---

## 🎨 Custom Colors

Gunakan dengan prefix `bg-`, `text-`, `border-`, dll:

```
invoice-primary     → Biru (#3B82F6)
invoice-secondary   → Ungu (#8B5CF6)
invoice-success     → Hijau (#10B981)
invoice-warning     → Kuning (#F59E0B)
invoice-danger      → Merah (#EF4444)
invoice-dark        → Hitam (#1F2937)
invoice-light       → Abu-abu terang (#F9FAFB)
```

**Contoh:**
```html
<div class="bg-invoice-primary text-white">Background biru</div>
<h1 class="text-invoice-secondary">Text ungu</h1>
<button class="border-2 border-invoice-success">Border hijau</button>
```

---

## 🔘 Buttons

### Class Tersedia:
- `.btn` - Base button
- `.btn-primary` - Button biru
- `.btn-secondary` - Button ungu
- `.btn-success` - Button hijau
- `.btn-danger` - Button merah
- `.btn-outline` - Button outline biru

**Contoh:**
```html
<button class="btn btn-primary">Simpan</button>
<button class="btn btn-danger">Hapus</button>
<button class="btn btn-outline">Batal</button>
```

---

## 📦 Cards

### Class Tersedia:
- `.card` - Container card
- `.card-header` - Header card dengan border bawah

**Contoh:**
```html
<div class="card">
    <div class="card-header">Judul Card</div>
    <p>Konten card di sini...</p>
</div>
```

---

## 📝 Forms

### Class Tersedia:
- `.form-group` - Container untuk 1 field
- `.form-label` - Label field
- `.form-input` - Input text
- `.form-select` - Select dropdown
- `.form-textarea` - Textarea

**Contoh:**
```html
<div class="form-group">
    <label class="form-label">Nama Produk</label>
    <input type="text" class="form-input" placeholder="Masukkan nama">
</div>

<div class="form-group">
    <label class="form-label">Kategori</label>
    <select class="form-select">
        <option>Pilih kategori</option>
    </select>
</div>

<div class="form-group">
    <label class="form-label">Deskripsi</label>
    <textarea class="form-textarea" rows="4"></textarea>
</div>
```

---

## 📊 Tables

### Class Tersedia:
- `.table` - Table base

**Contoh:**
```html
<table class="table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Harga</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>Laptop</td>
            <td>Rp 5.000.000</td>
        </tr>
    </tbody>
</table>
```

---

## 🏷️ Badges

### Class Tersedia:
- `.badge` + `.badge-success` - Badge hijau
- `.badge` + `.badge-warning` - Badge kuning
- `.badge` + `.badge-danger` - Badge merah
- `.badge` + `.badge-info` - Badge biru

**Contoh:**
```html
<span class="badge badge-success">Lunas</span>
<span class="badge badge-warning">Pending</span>
<span class="badge badge-danger">Belum Bayar</span>
```

---

## ⚠️ Alerts

### Class Tersedia:
- `.alert` + `.alert-success` - Alert hijau
- `.alert` + `.alert-warning` - Alert kuning
- `.alert` + `.alert-danger` - Alert merah
- `.alert` + `.alert-info` - Alert biru

**Contoh:**
```html
<div class="alert alert-success">
    Data berhasil disimpan!
</div>

<div class="alert alert-danger">
    Terjadi kesalahan!
</div>
```

---

## 🧭 Navigation

### Class Tersedia:
- `.nav-link` - Link navigasi
- `.nav-link.active` - Link aktif

**Contoh:**
```html
<nav>
    <a href="#" class="nav-link active">Dashboard</a>
    <a href="#" class="nav-link">Produk</a>
    <a href="#" class="nav-link">Invoice</a>
</nav>
```

---

## 🧾 Invoice Specific

### Class Tersedia:
- `.invoice-header` - Header invoice dengan gradient
- `.invoice-table` - Table khusus invoice
- `.invoice-total` - Total invoice (align right, bold)

**Contoh:**
```html
<div class="card">
    <div class="invoice-header">
        <h1>INVOICE #001</h1>
    </div>
    
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Laptop</td>
                <td>1</td>
                <td>Rp 5.000.000</td>
                <td>Rp 5.000.000</td>
            </tr>
        </tbody>
    </table>
    
    <div class="invoice-total">
        Total: Rp 5.000.000
    </div>
</div>
```

---

## ✨ Utility Classes

### Class Tersedia:
- `.text-gradient` - Text dengan gradient biru-ungu
- `.glass-effect` - Efek glassmorphism
- `.shadow-glow` - Shadow dengan glow effect
- `.no-print` - Hide saat print

**Contoh:**
```html
<h1 class="text-gradient text-4xl font-bold">
    Judul dengan Gradient
</h1>

<div class="glass-effect p-6 rounded-xl">
    Card dengan efek glass
</div>

<button class="btn btn-primary shadow-glow">
    Button dengan glow
</button>

<div class="no-print">
    Ini tidak akan muncul saat print
</div>
```

---

## 🎨 Tailwind Utility Classes (Tetap Bisa Dipakai!)

Selain custom class di atas, semua utility class Tailwind tetap bisa dipakai:

### Layout:
```html
<div class="flex justify-between items-center">...</div>
<div class="grid grid-cols-3 gap-4">...</div>
<div class="container mx-auto px-4">...</div>
```

### Spacing:
```html
<div class="p-4">Padding 1rem</div>
<div class="m-8">Margin 2rem</div>
<div class="mt-2 mb-4">Margin top & bottom</div>
```

### Typography:
```html
<h1 class="text-2xl font-bold">Judul</h1>
<p class="text-sm text-gray-600">Paragraph kecil</p>
<span class="uppercase tracking-wide">UPPERCASE</span>
```

### Colors:
```html
<div class="bg-blue-500 text-white">...</div>
<div class="bg-gray-100 text-gray-900">...</div>
```

### Responsive:
```html
<div class="w-full md:w-1/2 lg:w-1/3">
    Full width di mobile, 50% di tablet, 33% di desktop
</div>

<div class="hidden md:block">
    Hidden di mobile, visible di tablet+
</div>
```

### Hover & Focus:
```html
<button class="bg-blue-500 hover:bg-blue-600 active:scale-95">
    Hover effect
</button>

<input class="border focus:border-blue-500 focus:ring-2">
```

---

## 📱 Responsive Breakpoints

```
sm:   640px   (Mobile landscape)
md:   768px   (Tablet)
lg:   1024px  (Desktop)
xl:   1280px  (Large desktop)
2xl:  1536px  (Extra large)
```

**Contoh:**
```html
<div class="text-sm md:text-base lg:text-lg">
    Text kecil di mobile, sedang di tablet, besar di desktop
</div>
```

---

## 💡 Tips

1. **Kombinasikan custom class dengan Tailwind utility:**
   ```html
   <button class="btn btn-primary mt-4 w-full md:w-auto">
       Button full width di mobile, auto di desktop
   </button>
   ```

2. **Gunakan responsive classes:**
   ```html
   <div class="card p-4 md:p-6 lg:p-8">
       Padding bertambah di layar lebih besar
   </div>
   ```

3. **Stack utility classes:**
   ```html
   <div class="flex items-center gap-2 bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all">
       Kombinasi banyak utility
   </div>
   ```

---

## 📚 Resources

- **TailwindCSS Docs:** https://tailwindcss.com/docs
- **Cheat Sheet:** https://nerdcave.com/tailwind-cheat-sheet
- **Color Reference:** https://tailwindcss.com/docs/customizing-colors

---

Happy coding! 🚀
