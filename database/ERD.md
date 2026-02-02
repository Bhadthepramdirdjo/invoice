# 📊 Entity Relationship Diagram (ERD)

## Invoice App Database Structure

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         INVOICE APP - ERD                                   │
└─────────────────────────────────────────────────────────────────────────────┘


┌──────────────────┐
│   categories     │
├──────────────────┤
│ PK  id           │
│     name         │
│     description  │
│     created_at   │
│     updated_at   │
└──────────────────┘
         │
         │ 1:N
         ▼
┌──────────────────┐
│    products      │
├──────────────────┤
│ PK  id           │
│ FK  category_id  │───────┐
│     code         │       │
│     name         │       │
│     description  │       │
│     price        │       │
│     unit         │       │
│     stock        │       │
│     min_stock    │       │
│     is_active    │       │
│     created_at   │       │
│     updated_at   │       │
└──────────────────┘       │
         │                 │
         │ 1:N             │
         ▼                 │
┌──────────────────┐       │
│ invoice_items    │       │
├──────────────────┤       │
│ PK  id           │       │
│ FK  invoice_id   │───┐   │
│ FK  product_id   │◄──┘   │
│     product_code │       │
│     product_name │       │
│     description  │       │
│     quantity     │       │
│     unit         │       │
│     unit_price   │       │
│     discount_*   │       │
│     subtotal     │       │
│     sort_order   │       │
│     created_at   │       │
│     updated_at   │       │
└──────────────────┘       │
         ▲                 │
         │ N:1             │
         │                 │
┌──────────────────┐       │
│    invoices      │       │
├──────────────────┤       │
│ PK  id           │◄──────┘
│     invoice_num  │
│ FK  customer_id  │───────┐
│     customer_*   │       │
│     invoice_date │       │
│     due_date     │       │
│     subtotal     │       │
│     discount_*   │       │
│     tax_rate     │       │
│     tax_amount   │       │
│     total        │       │
│     paid_amount  │       │
│     status       │       │
│     payment_*    │       │
│     notes        │       │
│     terms        │       │
│     created_by   │───┐   │
│     created_at   │   │   │
│     updated_at   │   │   │
└──────────────────┘   │   │
         │             │   │
         │ 1:N         │   │
         ▼             │   │
┌──────────────────┐   │   │
│    payments      │   │   │
├──────────────────┤   │   │
│ PK  id           │   │   │
│ FK  invoice_id   │◄──┘   │
│     payment_num  │       │
│     payment_date │       │
│     amount       │       │
│     payment_meth │       │
│     reference_no │       │
│     notes        │       │
│     created_by   │───┐   │
│     created_at   │   │   │
│     updated_at   │   │   │
└──────────────────┘   │   │
                       │   │
                       │   │
┌──────────────────┐   │   │
│    customers     │   │   │
├──────────────────┤   │   │
│ PK  id           │◄──┘   │
│     code         │       │
│     name         │       │
│     company      │       │
│     email        │       │
│     phone        │       │
│     address      │       │
│     city         │       │
│     province     │       │
│     postal_code  │       │
│     tax_id       │       │
│     notes        │       │
│     is_active    │       │
│     created_at   │       │
│     updated_at   │       │
└──────────────────┘       │
                           │
                           │
┌──────────────────┐       │
│      users       │       │
├──────────────────┤       │
│ PK  id           │◄──────┘
│     username     │
│     password     │
│     full_name    │
│     email        │
│     role         │
│     is_active    │
│     last_login   │
│     created_at   │
│     updated_at   │
└──────────────────┘


┌──────────────────────────┐
│   company_settings       │
├──────────────────────────┤
│ PK  id                   │
│     company_name         │
│     company_address      │
│     company_city         │
│     company_province     │
│     company_postal_code  │
│     company_phone        │
│     company_email        │
│     company_website      │
│     company_tax_id       │
│     company_logo         │
│     invoice_prefix       │
│     invoice_number_fmt   │
│     invoice_next_number  │
│     tax_rate             │
│     currency             │
│     invoice_terms        │
│     invoice_notes        │
│     created_at           │
│     updated_at           │
└──────────────────────────┘


┌──────────────────┐
│ activity_logs    │
├──────────────────┤
│ PK  id           │
│     user_id      │
│     action       │
│     table_name   │
│     record_id    │
│     description  │
│     ip_address   │
│     user_agent   │
│     created_at   │
└──────────────────┘
```

---

## 🔗 Relationships

### **1. categories → products (1:N)**
- Satu kategori bisa memiliki banyak produk
- Foreign Key: `products.category_id` → `categories.id`
- ON DELETE: SET NULL

### **2. products → invoice_items (1:N)**
- Satu produk bisa ada di banyak invoice items
- Foreign Key: `invoice_items.product_id` → `products.id`
- ON DELETE: SET NULL

### **3. invoices → invoice_items (1:N)**
- Satu invoice memiliki banyak items
- Foreign Key: `invoice_items.invoice_id` → `invoices.id`
- ON DELETE: CASCADE

### **4. customers → invoices (1:N)**
- Satu customer bisa memiliki banyak invoice
- Foreign Key: `invoices.customer_id` → `customers.id`
- ON DELETE: SET NULL

### **5. invoices → payments (1:N)**
- Satu invoice bisa memiliki banyak payments (cicilan)
- Foreign Key: `payments.invoice_id` → `invoices.id`
- ON DELETE: CASCADE

### **6. users → invoices (1:N)**
- Satu user bisa membuat banyak invoice
- Foreign Key: `invoices.created_by` → `users.id`
- ON DELETE: SET NULL (optional)

### **7. users → payments (1:N)**
- Satu user bisa mencatat banyak payments
- Foreign Key: `payments.created_by` → `users.id`
- ON DELETE: SET NULL (optional)

---

## 📊 Cardinality Summary

```
categories (1) ──────< (N) products
products (1) ────────< (N) invoice_items
invoices (1) ────────< (N) invoice_items
customers (1) ───────< (N) invoices
invoices (1) ────────< (N) payments
users (1) ───────────< (N) invoices
users (1) ───────────< (N) payments
```

---

## 🔄 Data Flow

### **Create Invoice Flow:**

```
1. Select Customer
   └─> customers table

2. Add Products to Invoice
   └─> products table (get price)
   
3. Create Invoice Header
   └─> invoices table
   
4. Add Invoice Items
   └─> invoice_items table
   
5. Trigger Auto-Calculate
   └─> Update invoices.total
   
6. Generate Invoice Number
   └─> sp_generate_invoice_number()
   └─> Update company_settings.invoice_next_number
```

### **Payment Flow:**

```
1. Select Invoice
   └─> invoices table

2. Record Payment
   └─> payments table
   
3. Update Invoice
   └─> invoices.paid_amount
   └─> invoices.status
```

---

## 🎯 Key Features

### **1. Data Snapshot**
- Customer data di-snapshot ke `invoices` table
- Product data di-snapshot ke `invoice_items` table
- **Benefit:** Data tetap konsisten meski master data berubah

### **2. Auto-Calculate**
- Triggers otomatis hitung total invoice
- Saat item ditambah/diupdate/dihapus
- **Benefit:** Data selalu akurat

### **3. Soft Delete**
- Foreign keys menggunakan SET NULL
- Data tidak hilang saat master dihapus
- **Benefit:** Data historis tetap utuh

### **4. Audit Trail**
- `created_at` dan `updated_at` di semua tabel
- `activity_logs` untuk tracking
- **Benefit:** Mudah tracking perubahan

---

## 📝 Notes

- **PK** = Primary Key
- **FK** = Foreign Key
- **1:N** = One to Many
- **N:1** = Many to One
- **◄──** = Foreign Key relationship
- **───>** = Points to parent table

---

**Created:** 2026-02-02  
**Version:** 1.0
