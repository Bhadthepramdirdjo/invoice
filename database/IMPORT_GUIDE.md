# 🚀 Quick Guide - Import Database

Panduan cepat untuk import database Invoice App ke phpMyAdmin.

---

## 📋 Langkah-Langkah Import

### **Step 1: Buka phpMyAdmin**

1. Pastikan XAMPP sudah running (Apache & MySQL)
2. Buka browser, ketik:
   ```
   http://localhost/phpmyadmin
   ```

### **Step 2: Import File SQL**

1. **Klik tab "Import"** di menu atas
2. **Klik tombol "Choose File"** atau "Pilih File"
3. **Pilih file:** `database/invoice.sql`
4. **Scroll ke bawah**
5. **Klik tombol "Go"** atau **"Kirim"**

### **Step 3: Tunggu Proses Import**

- Proses import akan berjalan (biasanya 5-10 detik)
- Jika berhasil, akan muncul pesan sukses

### **Step 4: Verifikasi Database**

1. **Lihat di sidebar kiri**, database `invoice_app` akan muncul
2. **Klik database `invoice_app`**
3. **Cek tabel-tabel yang dibuat:**
   - ✅ categories (5 rows)
   - ✅ products (8 rows)
   - ✅ customers (3 rows)
   - ✅ invoices (1 row)
   - ✅ invoice_items (2 rows)
   - ✅ payments (0 rows)
   - ✅ users (1 row)
   - ✅ company_settings (1 row)
   - ✅ activity_logs (0 rows)

---

## ✅ Apa yang Sudah Dibuat?

### **Database:**
- Nama: `invoice_app`
- Character Set: `utf8mb4`
- Collation: `utf8mb4_unicode_ci`

### **Tabel (9 tabel):**
1. **categories** - Kategori produk
2. **products** - Daftar produk/price list
3. **customers** - Data pelanggan
4. **invoices** - Invoice utama
5. **invoice_items** - Detail item invoice
6. **payments** - Riwayat pembayaran
7. **users** - Pengguna sistem
8. **company_settings** - Pengaturan perusahaan
9. **activity_logs** - Log aktivitas

### **Views (3 views):**
1. **view_invoice_summary** - Ringkasan invoice
2. **view_product_stock** - Status stok produk
3. **view_customer_summary** - Ringkasan per customer

### **Stored Procedures:**
1. **sp_generate_invoice_number()** - Generate nomor invoice otomatis

### **Triggers:**
1. **tr_update_invoice_total_after_insert** - Auto update total saat item ditambah
2. **tr_update_invoice_total_after_update** - Auto update total saat item diupdate
3. **tr_update_invoice_total_after_delete** - Auto update total saat item dihapus

### **Sample Data:**
- ✅ 5 Kategori produk
- ✅ 8 Produk contoh
- ✅ 3 Customer contoh
- ✅ 1 Invoice contoh
- ✅ 1 User admin
- ✅ 1 Company settings

---

## 🔐 Login Default

**Username:** `admin`  
**Password:** `admin123`

*(Password sudah di-hash dengan bcrypt)*

---

## 🧪 Test Database

### **Test 1: Lihat Produk**
```sql
SELECT * FROM products;
```

### **Test 2: Lihat Invoice dengan Items**
```sql
SELECT 
  i.invoice_number,
  i.customer_name,
  i.total,
  ii.product_name,
  ii.quantity,
  ii.unit_price
FROM invoices i
LEFT JOIN invoice_items ii ON i.id = ii.invoice_id;
```

### **Test 3: Lihat View Invoice Summary**
```sql
SELECT * FROM view_invoice_summary;
```

### **Test 4: Generate Invoice Number**
```sql
CALL sp_generate_invoice_number();
```

---

## 📝 Konfigurasi Database di PHP

File konfigurasi sudah dibuat di: `config/database.php`

**Default Settings:**
```php
DB_HOST: localhost
DB_USER: root
DB_PASS: (kosong)
DB_NAME: invoice_app
```

**Jika password MySQL Anda berbeda**, edit file `config/database.php`:
```php
define('DB_PASS', 'password_anda');
```

---

## 🔧 Troubleshooting

### **Error: Database sudah ada**
**Solusi:**
1. Drop database lama dulu:
   ```sql
   DROP DATABASE IF EXISTS invoice_app;
   ```
2. Import ulang file SQL

### **Error: Max execution time**
**Solusi:**
1. Di phpMyAdmin, klik tab "Import"
2. Scroll ke bawah
3. Centang "Enable the linter"
4. Coba import lagi

### **Error: Foreign key constraint**
**Solusi:**
- Pastikan import file SQL lengkap dari awal sampai akhir
- Jangan import sebagian-sebagian

---

## 📚 Dokumentasi Lengkap

Untuk dokumentasi lengkap database schema, lihat:
```
database/DATABASE_SCHEMA.md
```

---

## ✅ Checklist Setelah Import

- [ ] Database `invoice_app` sudah dibuat
- [ ] 9 tabel sudah dibuat
- [ ] 3 views sudah dibuat
- [ ] 1 stored procedure sudah dibuat
- [ ] 3 triggers sudah dibuat
- [ ] Sample data sudah ada
- [ ] File `config/database.php` sudah dibuat
- [ ] Test koneksi database dari PHP

---

## 🎯 Next Steps

Setelah database berhasil di-import:

1. ✅ **Test koneksi database** dari PHP
2. ✅ **Mulai buat halaman-halaman** dengan TailwindCSS
3. ✅ **Implementasi CRUD** untuk produk, customer, invoice

---

## 💡 Tips

- **Backup database** secara berkala via phpMyAdmin → Export
- **Gunakan views** untuk query yang kompleks
- **Gunakan stored procedure** untuk generate invoice number
- **Triggers akan otomatis** update total invoice

---

**Selamat! Database sudah siap digunakan!** 🎉

Jika ada pertanyaan, silakan tanya! 😊
