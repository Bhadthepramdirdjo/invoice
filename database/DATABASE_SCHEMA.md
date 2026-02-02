# 📊 Database Schema Documentation - Invoice App

## 📋 Overview

Database untuk aplikasi Invoice App dengan fitur lengkap manajemen produk, customer, dan invoice.

**Database Name:** `invoice_app`  
**Character Set:** `utf8mb4`  
**Collation:** `utf8mb4_unicode_ci`

---

## 🗂️ Tabel-Tabel

### 1. **categories** - Kategori Produk

Menyimpan kategori untuk produk/jasa.

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT(11) PK | ID kategori |
| `name` | VARCHAR(100) UNIQUE | Nama kategori |
| `description` | TEXT | Deskripsi kategori |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Sample Data:**
- Elektronik
- Makanan & Minuman
- Pakaian
- Jasa
- Lainnya

---

### 2. **products** - Daftar Produk (Price List)

Menyimpan daftar produk/jasa dengan harga.

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT(11) PK | ID produk |
| `category_id` | INT(11) FK | ID kategori |
| `code` | VARCHAR(50) UNIQUE | Kode produk |
| `name` | VARCHAR(255) | Nama produk |
| `description` | TEXT | Deskripsi produk |
| `price` | DECIMAL(15,2) | Harga satuan |
| `unit` | VARCHAR(50) | Satuan (pcs, kg, box, dll) |
| `stock` | INT(11) | Stok tersedia |
| `min_stock` | INT(11) | Minimum stok |
| `is_active` | TINYINT(1) | Status aktif (1=aktif, 0=nonaktif) |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Foreign Keys:**
- `category_id` → `categories(id)` ON DELETE SET NULL

**Sample Data:**
- Laptop ASUS ROG (Rp 15.000.000)
- Mouse Logitech (Rp 1.250.000)
- Keyboard Mechanical (Rp 1.500.000)

---

### 3. **customers** - Data Pelanggan

Menyimpan informasi pelanggan.

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT(11) PK | ID customer |
| `code` | VARCHAR(50) UNIQUE | Kode customer |
| `name` | VARCHAR(255) | Nama customer |
| `company` | VARCHAR(255) | Nama perusahaan |
| `email` | VARCHAR(255) | Email |
| `phone` | VARCHAR(50) | Nomor telepon |
| `address` | TEXT | Alamat lengkap |
| `city` | VARCHAR(100) | Kota |
| `province` | VARCHAR(100) | Provinsi |
| `postal_code` | VARCHAR(20) | Kode pos |
| `tax_id` | VARCHAR(50) | NPWP |
| `notes` | TEXT | Catatan |
| `is_active` | TINYINT(1) | Status aktif |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Sample Data:**
- Budi Santoso (PT. Maju Jaya)
- Siti Nurhaliza (CV. Berkah Selalu)
- Ahmad Fauzi (UD. Sejahtera Abadi)

---

### 4. **invoices** - Invoice Utama

Menyimpan data invoice header.

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT(11) PK | ID invoice |
| `invoice_number` | VARCHAR(50) UNIQUE | Nomor invoice |
| `customer_id` | INT(11) FK | ID customer |
| `customer_name` | VARCHAR(255) | Nama customer (snapshot) |
| `customer_company` | VARCHAR(255) | Perusahaan customer |
| `customer_email` | VARCHAR(255) | Email customer |
| `customer_phone` | VARCHAR(50) | Telepon customer |
| `customer_address` | TEXT | Alamat customer |
| `customer_city` | VARCHAR(100) | Kota customer |
| `customer_tax_id` | VARCHAR(50) | NPWP customer |
| `invoice_date` | DATE | Tanggal invoice |
| `due_date` | DATE | Tanggal jatuh tempo |
| `subtotal` | DECIMAL(15,2) | Subtotal sebelum pajak |
| `discount_type` | ENUM | Tipe diskon (percentage/fixed) |
| `discount_value` | DECIMAL(15,2) | Nilai diskon |
| `discount_amount` | DECIMAL(15,2) | Jumlah diskon |
| `tax_rate` | DECIMAL(5,2) | Rate PPN (%) |
| `tax_amount` | DECIMAL(15,2) | Jumlah PPN |
| `total` | DECIMAL(15,2) | Total invoice |
| `paid_amount` | DECIMAL(15,2) | Jumlah yang sudah dibayar |
| `status` | ENUM | Status (draft/sent/paid/partial/overdue/cancelled) |
| `payment_method` | VARCHAR(100) | Metode pembayaran |
| `payment_date` | DATE | Tanggal pembayaran |
| `notes` | TEXT | Catatan |
| `terms` | TEXT | Syarat & ketentuan |
| `created_by` | INT(11) | ID user pembuat |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Foreign Keys:**
- `customer_id` → `customers(id)` ON DELETE SET NULL

**Status Values:**
- `draft` - Draft, belum dikirim
- `sent` - Sudah dikirim ke customer
- `paid` - Sudah lunas
- `partial` - Dibayar sebagian
- `overdue` - Jatuh tempo
- `cancelled` - Dibatalkan

---

### 5. **invoice_items** - Detail Item Invoice

Menyimpan detail item per invoice.

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT(11) PK | ID item |
| `invoice_id` | INT(11) FK | ID invoice |
| `product_id` | INT(11) FK | ID produk |
| `product_code` | VARCHAR(50) | Kode produk (snapshot) |
| `product_name` | VARCHAR(255) | Nama produk (snapshot) |
| `description` | TEXT | Deskripsi |
| `quantity` | DECIMAL(10,2) | Jumlah/qty |
| `unit` | VARCHAR(50) | Satuan |
| `unit_price` | DECIMAL(15,2) | Harga satuan |
| `discount_type` | ENUM | Tipe diskon |
| `discount_value` | DECIMAL(15,2) | Nilai diskon |
| `discount_amount` | DECIMAL(15,2) | Jumlah diskon |
| `subtotal` | DECIMAL(15,2) | Subtotal (qty × price - discount) |
| `sort_order` | INT(11) | Urutan tampilan |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Foreign Keys:**
- `invoice_id` → `invoices(id)` ON DELETE CASCADE
- `product_id` → `products(id)` ON DELETE SET NULL

**Note:** Data produk di-snapshot agar tetap konsisten meski produk berubah.

---

### 6. **payments** - Riwayat Pembayaran

Menyimpan riwayat pembayaran invoice.

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT(11) PK | ID payment |
| `invoice_id` | INT(11) FK | ID invoice |
| `payment_number` | VARCHAR(50) | Nomor pembayaran |
| `payment_date` | DATE | Tanggal pembayaran |
| `amount` | DECIMAL(15,2) | Jumlah dibayar |
| `payment_method` | VARCHAR(100) | Metode pembayaran |
| `reference_number` | VARCHAR(100) | No. referensi bank |
| `notes` | TEXT | Catatan |
| `created_by` | INT(11) | ID user |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Foreign Keys:**
- `invoice_id` → `invoices(id)` ON DELETE CASCADE

---

### 7. **users** - Pengguna Sistem

Menyimpan data pengguna sistem (opsional untuk multi-user).

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT(11) PK | ID user |
| `username` | VARCHAR(100) UNIQUE | Username |
| `password` | VARCHAR(255) | Password (hashed) |
| `full_name` | VARCHAR(255) | Nama lengkap |
| `email` | VARCHAR(255) UNIQUE | Email |
| `role` | ENUM | Role (admin/staff/viewer) |
| `is_active` | TINYINT(1) | Status aktif |
| `last_login` | TIMESTAMP | Login terakhir |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

**Default User:**
- Username: `admin`
- Password: `admin123`
- Role: `admin`

---

### 8. **company_settings** - Pengaturan Perusahaan

Menyimpan informasi perusahaan dan pengaturan invoice.

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT(11) PK | ID setting |
| `company_name` | VARCHAR(255) | Nama perusahaan |
| `company_address` | TEXT | Alamat perusahaan |
| `company_city` | VARCHAR(100) | Kota |
| `company_province` | VARCHAR(100) | Provinsi |
| `company_postal_code` | VARCHAR(20) | Kode pos |
| `company_phone` | VARCHAR(50) | Telepon |
| `company_email` | VARCHAR(255) | Email |
| `company_website` | VARCHAR(255) | Website |
| `company_tax_id` | VARCHAR(50) | NPWP |
| `company_logo` | VARCHAR(255) | Path logo |
| `invoice_prefix` | VARCHAR(20) | Prefix invoice (INV) |
| `invoice_number_format` | VARCHAR(50) | Format nomor ({PREFIX}-{YEAR}-{NUMBER}) |
| `invoice_next_number` | INT(11) | Nomor invoice berikutnya |
| `tax_rate` | DECIMAL(5,2) | Rate PPN default (11%) |
| `currency` | VARCHAR(10) | Mata uang (IDR) |
| `invoice_terms` | TEXT | Syarat & ketentuan default |
| `invoice_notes` | TEXT | Catatan default |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu diupdate |

---

### 9. **activity_logs** - Log Aktivitas

Menyimpan log aktivitas sistem (opsional).

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT(11) PK | ID log |
| `user_id` | INT(11) | ID user |
| `action` | VARCHAR(100) | Aksi (create/update/delete) |
| `table_name` | VARCHAR(100) | Nama tabel |
| `record_id` | INT(11) | ID record |
| `description` | TEXT | Deskripsi |
| `ip_address` | VARCHAR(50) | IP address |
| `user_agent` | VARCHAR(255) | User agent |
| `created_at` | TIMESTAMP | Waktu dibuat |

---

## 📊 Views

### 1. **view_invoice_summary**

Ringkasan invoice dengan total items dan balance.

**Columns:**
- `id`, `invoice_number`, `invoice_date`, `due_date`
- `customer_name`, `customer_company`
- `subtotal`, `tax_amount`, `total`, `paid_amount`, `balance`
- `status`, `total_items`, `created_at`

### 2. **view_product_stock**

Status stok produk dengan kategori.

**Columns:**
- `id`, `code`, `name`, `category_name`
- `price`, `unit`, `stock`, `min_stock`
- `stock_status` (Habis/Stok Menipis/Tersedia)
- `is_active`

### 3. **view_customer_summary**

Ringkasan invoice per customer.

**Columns:**
- `id`, `code`, `name`, `company`, `email`, `phone`
- `total_invoices`, `total_amount`, `total_paid`, `total_balance`
- `is_active`

---

## ⚙️ Stored Procedures

### **sp_generate_invoice_number()**

Generate nomor invoice otomatis berdasarkan format di company_settings.

**Usage:**
```sql
CALL sp_generate_invoice_number();
```

**Output:**
```
invoice_number
--------------
INV-2026-0001
```

---

## 🔄 Triggers

### **tr_update_invoice_total_after_insert**
Auto update total invoice saat item ditambah.

### **tr_update_invoice_total_after_update**
Auto update total invoice saat item diupdate.

### **tr_update_invoice_total_after_delete**
Auto update total invoice saat item dihapus.

**Cara Kerja:**
1. Hitung subtotal dari semua items
2. Hitung tax_amount = subtotal × tax_rate
3. Hitung total = subtotal + tax_amount
4. Update tabel invoices

---

## 📈 Relationships (ERD)

```
categories
    ↓ (1:N)
products
    ↓ (1:N)
invoice_items
    ↑ (N:1)
invoices ← (N:1) → customers
    ↓ (1:N)
payments
```

**Penjelasan:**
- 1 kategori bisa punya banyak produk
- 1 produk bisa ada di banyak invoice items
- 1 invoice punya banyak items
- 1 customer bisa punya banyak invoice
- 1 invoice bisa punya banyak payments

---

## 🚀 Cara Import ke phpMyAdmin

### **Step 1: Buka phpMyAdmin**
```
http://localhost/phpmyadmin
```

### **Step 2: Import Database**
1. Klik tab **"Import"**
2. Klik **"Choose File"**
3. Pilih file: `database/invoice.sql`
4. Klik **"Go"** / **"Kirim"**

### **Step 3: Verifikasi**
1. Database `invoice_app` akan dibuat
2. Semua tabel akan dibuat
3. Data contoh akan diinsert

---

## 📝 Query Examples

### Get All Products with Category
```sql
SELECT p.*, c.name AS category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.is_active = 1
ORDER BY p.name;
```

### Get Invoice with Items
```sql
SELECT 
  i.*,
  ii.product_name,
  ii.quantity,
  ii.unit_price,
  ii.subtotal
FROM invoices i
LEFT JOIN invoice_items ii ON i.id = ii.invoice_id
WHERE i.invoice_number = 'INV-2026-001';
```

### Get Customer Outstanding Balance
```sql
SELECT 
  customer_name,
  SUM(total - paid_amount) AS outstanding
FROM invoices
WHERE status IN ('sent', 'partial', 'overdue')
GROUP BY customer_id, customer_name;
```

### Get Products Low Stock
```sql
SELECT * FROM view_product_stock
WHERE stock_status IN ('Habis', 'Stok Menipis')
ORDER BY stock ASC;
```

---

## 🔐 Security Notes

1. **Password Hashing:** User password menggunakan bcrypt (`$2y$10$`)
2. **Foreign Keys:** Menggunakan CASCADE dan SET NULL untuk data integrity
3. **Indexes:** Sudah ada indexes untuk query optimization
4. **Validation:** Perlu validasi di aplikasi PHP untuk input data

---

## 📊 Sample Data

Database sudah include sample data:
- ✅ 5 Kategori
- ✅ 8 Produk
- ✅ 3 Customer
- ✅ 1 Invoice dengan 2 items
- ✅ 1 User (admin)
- ✅ 1 Company settings

---

## 🔧 Maintenance

### Backup Database
```sql
-- Via phpMyAdmin: Export → SQL
-- Via command line:
mysqldump -u root -p invoice_app > backup.sql
```

### Reset Auto Increment
```sql
ALTER TABLE invoices AUTO_INCREMENT = 1;
```

### Clear All Data (Keep Structure)
```sql
TRUNCATE TABLE invoice_items;
TRUNCATE TABLE invoices;
TRUNCATE TABLE products;
TRUNCATE TABLE customers;
-- dst...
```

---

## 📞 Support

Jika ada pertanyaan tentang database schema, silakan tanya! 😊

---

**Created:** 2026-02-02  
**Version:** 1.0  
**Database:** MySQL 5.7+ / MariaDB 10.2+
