# Invoice App

Aplikasi manajemen invoice berbasis web sederhana yang dibangun menggunakan PHP Native dan Tailwind CSS. Aplikasi ini dirancang untuk membantu pemilik bisnis kecil dan menengah dalam mengelola produk, pelanggan, dan pembuatan tagihan (invoice) dengan mudah.

## Fitur Utama

*   **Dashboard Interaktif**: Menampilkan ringkasan total pendapatan, jumlah invoice, dan status pembayaran.
*   **Manajemen Produk**: Tambah, ubah, dan hapus data produk atau layanan.
*   **Manajemen Pelanggan (Customer)**: Database pelanggan untuk memudahkan pembuatan invoice berulang.
*   **Pembuatan Invoice**:
    *   Input produk otomatis dengan kalkulasi harga.
    *   Dukungan untuk input pelanggan manual atau dari database.
    *   Status invoice (Draft, Sent, Paid).
*   **Generate PDF**: Cetak invoice ke dalam format PDF siap kirim.
*   **Desain Responsif**: Tampilan yang menyesuaikan dengan layar desktop maupun perangkat mobile (HP).

## Persyaratan Sistem

Untuk menjalankan aplikasi ini, pastikan komputer Anda memiliki:
*   PHP versi 7.4 atau terbaru.
*   MySQL Database.
*   Web Server (Apache/Nginx).
*   Disarankan menggunakan **XAMPP** atau **Laragon** untuk lingkungan lokal Windows.

## Instalasi

Ikuti langkah-langkah berikut untuk menginstal aplikasi di komputer lokal Anda:

1.  **Clone atau Unduh Repository**
    Letakkan folder proyek ini di dalam direktori root server lokal Anda (contoh: `d:\Xampp\htdocs\invoiceapp`).

2.  **Persiapan Database**
    *   Buka phpMyAdmin (biasanya di `http://localhost/phpmyadmin`).
    *   Buat database baru dengan nama `invoice_db`.
    *   Impor file `database/invoice.sql` ke dalam database yang baru dibuat.

3.  **Konfigurasi Koneksi**
    *   Buka file `config/database.php`.
    *   Sesuaikan pengaturan database jika Anda mengubah username/password default MySQL Anda.
    ```php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');      // User default XAMPP biasanya 'root'
    define('DB_PASS', '');          // Password default XAMPP biasanya kosong
    define('DB_NAME', 'invoice_db');
    ```

4.  **Jalankan Aplikasi**
    Buka browser dan akses alamat: `http://localhost/invoiceapp`

## Panduan Penggunaan

### 1. Dashboard
Halaman utama menampilkan ringkasan performa bisnis Anda, termasuk Total Pendapatan, Invoice Pending, dan Invoice Jatuh Tempo. Gunakan menu navigasi di atas (atau menu hamburger di mobile) untuk berpindah halaman.

### 2. Mengelola Produk
Masuk ke menu **Produk** untuk mengatur barang atau jasa yang Anda jual.
*   Klik **Tambah Produk** untuk memasukkan item baru.
*   Isi nama, kode (SKU), harga, dan stok.
*   Gunakan tombol Edit atau Hapus pada tabel untuk mengubah data.

### 3. Mengelola Customer
Masuk ke menu **Customer** untuk menyimpan data klien.
*   Klik **Tambah Customer** untuk mendaftarkan klien baru.
*   Formulir mencakup Nama, Perusahaan, Kontak, dan Alamat Lengkap.
*   Data ini akan muncul otomatis saat Anda membuat invoice.

### 4. Membuat Invoice
Masuk ke menu **Invoice** dan klik **Buat Invoice Baru**.

*   **Info Pengirim**: Secara default terisi dengan data perusahaan Anda (bisa diubah di kode).
*   **Pilih Customer**:
    *   Gunakan dropdown untuk memilih customer yang sudah tersimpan. Alamat akan terisi otomatis.
    *   Centang "Tulis manual" jika ingin membuat invoice untuk customer sekali lewat tanpa menyimpannya ke database.
*   **Isi Item**:
    *   Klik **+ Tambah Item** untuk menambah baris.
    *   Pilih produk dari dropdown, harga akan terisi otomatis namun tetap bisa diedit.
    *   Masukkan jumlah (qty), total per baris akan dihitung otomatis.
    *   Gunakan tombol **Hapus** (warna merah) untuk membuang baris item.
*   **Simpan**:
    *   **Simpan Draft**: Menyimpan invoice untuk diedit nanti.
    *   **Simpan & Cetak**: Menyimpan invoice dengan status 'Sent' dan membuka tinjauan PDF/Cetak.

### 5. Mencetak Invoice
Setelah invoice disimpan, Anda akan diarahkan ke halaman detail. Klik tombol **Print / PDF** di pojok kanan atas. Tampilan cetak telah dioptimalkan untuk kertas A4 dan menyembunyikan elemen navigasi browser.

## Struktur Folder

*   `api/` - Endpoint untuk data JSON (jika diperlukan untuk integrasi).
*   `config/` - Konfigurasi koneksi database.
*   `database/` - File SQL untuk skema database.
*   `page/` - Halaman-halaman utama aplikasi (invoices, products, customers).
*   `uploads/` - Folder penyimpanan gambar produk.
*   `index.php` - Halaman dashboard utama.

## Teknologi

*   **Backend**: PHP Native (PDO)
*   **Frontend**: HTML5, JavaScript
*   **Styling**: Tailwind CSS (CDN)
*   **Database**: MySQL