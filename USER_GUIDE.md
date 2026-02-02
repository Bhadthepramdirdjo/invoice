# 🚀 Invoice App - User Guide

## Panduan Lengkap Penggunaan Invoice App

---

## 📋 **Daftar Isi**

1. [Setup Awal](#setup-awal)
2. [Dashboard](#dashboard)
3. [Membuat Invoice](#membuat-invoice)
4. [Mengelola Produk](#mengelola-produk)
5. [Mengelola Customer](#mengelola-customer)

---

## 🔧 **Setup Awal**

### **1. Import Database**

1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Klik tab "Import"
3. Pilih file: `database/invoice.sql`
4. Klik "Go"

**Detail:** Lihat `database/IMPORT_GUIDE.md`

### **2. Konfigurasi Database**

File: `config/database.php`

```php
DB_HOST: localhost
DB_USER: root
DB_PASS: (kosong atau password MySQL Anda)
DB_NAME: invoice_app
```

### **3. Build TailwindCSS**

```powershell
.\watch.ps1
```

Biarkan terminal tetap terbuka saat development.

**Detail:** Lihat `README_TAILWIND.md`

### **4. Akses Aplikasi**

Buka browser:
```
http://localhost/invoiceapp/
```

---

## 🏠 **Dashboard**

### **URL:** `http://localhost/invoiceapp/`

Dashboard adalah halaman utama yang menampilkan:

### **1. Menu Cards**
- **Buat Invoice** → Langsung ke form create invoice
- **Daftar Invoice** → Lihat semua invoice
- **Produk & Price List** → Kelola produk
- **Customer** → Kelola customer

### **2. Quick Stats**
- **Invoice Bulan Ini** - Total invoice & nilai bulan ini
- **Belum Lunas** - Jumlah invoice yang belum dibayar
- **Stok Menipis** - Produk yang perlu restock

### **3. Invoice Terbaru**
- Tabel 5 invoice terbaru
- Quick action: Lihat detail

---

## 📄 **Membuat Invoice**

### **Langkah-Langkah:**

#### **1. Klik "Buat Invoice"** dari Dashboard

#### **2. Pilih Customer**
- Pilih dari dropdown customer yang sudah ada
- Atau klik "+ Tambah Customer Baru"
- Data customer akan otomatis terisi

#### **3. Tambah Item Invoice**
- Klik tombol **"Tambah Item"**
- Pilih produk dari dropdown
- Harga akan otomatis terisi dari database
- Atur quantity
- Subtotal akan otomatis dihitung

**Contoh:**
```
Produk: Laptop ASUS ROG
Qty: 2
Harga: Rp 15.000.000
Subtotal: Rp 30.000.000 (otomatis)
```

#### **4. Tambah Item Lainnya**
- Klik "Tambah Item" lagi untuk item berikutnya
- Bisa tambah banyak item sekaligus

#### **5. Atur Detail Invoice**
- **Tanggal Invoice:** Default hari ini
- **Jatuh Tempo:** Default 30 hari dari sekarang
- **PPN (%):** Default 11% (bisa diubah)

#### **6. Total Otomatis Dihitung**
```
Subtotal: Rp 30.000.000
PPN (11%): Rp 3.300.000
Total: Rp 33.300.000
```

#### **7. Tambah Catatan (Opsional)**
- Catatan untuk invoice ini
- Syarat & ketentuan

#### **8. Simpan Invoice**

**Pilihan:**
- **Simpan & Kirim** → Status: Sent (siap dikirim ke customer)
- **Simpan sebagai Draft** → Status: Draft (masih bisa diedit)

---

## 🎯 **Fitur Invoice**

### **Status Invoice:**
- **Draft** - Masih bisa diedit
- **Sent** - Sudah dikirim ke customer
- **Paid** - Sudah lunas
- **Partial** - Dibayar sebagian
- **Overdue** - Jatuh tempo
- **Cancelled** - Dibatalkan

### **Auto-Calculate:**
✅ Subtotal per item otomatis dihitung  
✅ Total invoice otomatis dihitung  
✅ PPN otomatis dihitung  
✅ Update real-time saat ubah qty/harga  

### **Data Snapshot:**
✅ Data customer di-snapshot ke invoice  
✅ Data produk di-snapshot ke invoice items  
✅ Benefit: Data tetap konsisten meski master berubah  

---

## 📦 **Mengelola Produk**

### **Akses:** Dashboard → Produk

### **Fitur:**
- Lihat daftar produk
- Tambah produk baru
- Edit produk
- Hapus produk
- Tracking stok
- Kategori produk

### **Data Produk:**
- Kode Produk
- Nama Produk
- Kategori
- Harga
- Satuan (pcs, kg, box, dll)
- Stok
- Minimum Stok (untuk alert)

---

## 👥 **Mengelola Customer**

### **Akses:** Dashboard → Customer

### **Fitur:**
- Lihat daftar customer
- Tambah customer baru
- Edit customer
- Hapus customer
- Lihat history invoice per customer

### **Data Customer:**
- Kode Customer
- Nama
- Perusahaan
- Email
- Telepon
- Alamat Lengkap
- NPWP

---

## 💡 **Tips & Tricks**

### **1. Workflow Efisien:**
```
1. Setup produk & price list dulu
2. Setup customer
3. Baru buat invoice
```

### **2. Gunakan Kategori:**
- Kelompokkan produk berdasarkan kategori
- Mudah cari produk saat buat invoice

### **3. Set Minimum Stok:**
- Akan muncul alert di dashboard
- Tahu kapan harus restock

### **4. Gunakan Draft:**
- Simpan sebagai draft jika belum yakin
- Bisa edit lagi nanti
- Kirim ke customer saat sudah siap

### **5. Snapshot Data:**
- Data customer & produk di-snapshot
- Aman meski data master berubah
- Invoice tetap konsisten

---

## 🔧 **Troubleshooting**

### **CSS tidak muncul?**
**Solusi:**
1. Pastikan watch mode berjalan: `.\watch.ps1`
2. Atau build manual: `.\build.ps1`
3. Hard refresh browser: `Ctrl + F5`

### **Database error?**
**Solusi:**
1. Cek koneksi database di `config/database.php`
2. Pastikan MySQL running
3. Pastikan database sudah di-import

### **Invoice tidak tersimpan?**
**Solusi:**
1. Pastikan minimal 1 item ditambahkan
2. Cek semua field required terisi
3. Cek console browser untuk error

---

## 📚 **Dokumentasi Lainnya**

- **Database Schema:** `database/DATABASE_SCHEMA.md`
- **ERD:** `database/ERD.md`
- **TailwindCSS Setup:** `README_TAILWIND.md`
- **Quick Reference:** `QUICK_REFERENCE.md`

---

## 🎉 **Selamat Menggunakan Invoice App!**

Jika ada pertanyaan atau kendala, silakan hubungi developer! 😊

---

**Version:** 1.0  
**Last Updated:** 2026-02-02
