# Database Refactor Data Dictionary (Final Draft)

## Scope
Dokumen ini adalah hasil audit schema existing + gap analysis untuk refactor non-destruktif pada Laravel 11 + PostgreSQL.

Prinsip utama:
- Tidak drop/rename tabel legacy di awal.
- Semua field bisnis penting dari schema lama dipertahankan.
- Target modular: master, operasional, dokumen, laporan.

## Legacy Tables (Source of Truth Saat Ini)

### barang
Purpose:
- Menjadi master campuran material + produk (dibedakan dari kolom status).

Columns penting:
- id_barang (PK, string custom BRGxxxxx)
- kode (unique)
- nama
- status (material|produk)
- quantity
- stok_minimum
- satuan
- ukuran
- panjang, lebar, tinggi
- material_type
- gambar_path
- id_jenis_barang
- created_at, updated_at

Dipakai oleh:
- BarangController
- BarangMasukController
- BarangDalamProsesController
- BarangKeluarController
- DashboardController
- LaporanStokMaterialController
- LaporanStokProdukController
- Banyak Blade view master/operasional/laporan

### barang_masuk
Purpose:
- Pencatatan material masuk + dokumen.

Columns penting:
- id_barang_masuk (PK, string custom BMSxxxxx)
- id_barang (FK -> barang.id_barang)
- id_supplier (FK -> suppliers.id_supplier)
- id_user (FK -> users.id_user)
- id_jenis_barang (FK -> jenis_barang.id_jenis_barang)
- quantity
- satuan
- status
- tanggal_masuk
- estimasi_tiba
- invoice_path
- surat_jalan_path
- gambar_path
- created_at, updated_at

Dipakai oleh:
- BarangMasukController
- DashboardController (activity)
- Laporan stok material (indirect)

### barang_dalam_proses
Purpose:
- Lifecycle produksi dari produk + konsumsi material.

Columns penting:
- id_barang_proses (PK)
- id_barang (produk FK -> barang)
- id_barang_mentah (material FK -> barang)
- id_customer (FK -> customers)
- id_user (FK -> users)
- id_barang_keluar (FK -> barang_keluar)
- quantity
- tanggal_selesai
- status_kirim
- processing, reserve_token, processing_started_at, processing_by
- satuan, ukuran, barang_mentah
- created_at, updated_at

Dipakai oleh:
- BarangDalamProsesController
- BarangKeluarController (prepare/cancel reserve)
- DashboardController

### barang_keluar
Purpose:
- Pengiriman produk (make-to-order), dokumen pengiriman.

Columns penting:
- id_barang_keluar (PK, string custom BKLxxxxx)
- id_barang (FK -> barang)
- id_customer (FK -> customers)
- id_user (FK -> users)
- id_barang_proses (FK -> barang_dalam_proses)
- quantity
- tanggal_keluar
- status_pengiriman
- invoice_path
- surat_jalan_path
- gambar_path
- material_type
- created_at, updated_at

Dipakai oleh:
- BarangKeluarController
- DashboardController
- LaporanPengirimanProdukController

### customers
Columns:
- id_customer (PK)
- nama
- jabatan
- alamat
- kontak
- email
- created_at, updated_at

Dipakai oleh:
- CustomerController
- BarangDalamProsesController
- BarangKeluarController

### suppliers
Columns:
- id_supplier (PK)
- nama
- jabatan
- alamat
- kontak
- email
- created_at, updated_at

Dipakai oleh:
- SupplierController
- BarangMasukController

### users
Columns:
- id_user (PK)
- name
- email (unique)
- password
- role
- remember_token
- created_at, updated_at

Dipakai oleh:
- Auth + RoleMiddleware
- Semua transaksi operasional (id_user)

## Target New Modular Tables (Recommended)

### materials
Minimal + preserve:
- id_material (PK)
- legacy_barang_id (unique, nullable setelah cutover)
- kode (unique)
- nama_material
- stok_saat_ini
- stok_minimum
- satuan
- ukuran
- panjang, lebar, tinggi
- material_type
- gambar_path
- id_jenis_barang (opsional)
- created_at, updated_at

### products
- id_produk (PK)
- legacy_barang_id (unique, nullable setelah cutover)
- kode (unique)
- nama_produk
- ukuran
- satuan
- panjang, lebar, tinggi
- gambar_path
- id_jenis_barang (opsional)
- created_at, updated_at

### material_orders (pemesanan_material)
- id_pemesanan (PK)
- legacy_barang_masuk_id (unique, nullable)
- id_material (FK)
- id_supplier (FK)
- id_user (FK)
- qty
- satuan
- status
- tgl_pemesanan
- estimasi_tiba
- invoice_path
- surat_jalan_path
- gambar_path
- created_at, updated_at

### production_items (barang_dalam_proses)
- id_barang_proses (PK)
- legacy_barang_proses_id (unique, nullable)
- id_produk (FK)
- id_material (FK)
- id_pelanggan (FK)
- id_user (FK)
- qty
- satuan
- ukuran
- tgl_dibuat
- tgl_selesai
- status_kirim
- processing
- reserve_token
- processing_started_at
- processing_by
- created_at, updated_at

### shipments (pengiriman_barang)
- id_pengiriman (PK)
- legacy_barang_keluar_id (unique, nullable)
- id_barang_proses (FK)
- id_produk (FK)
- id_pelanggan (FK)
- id_user (FK)
- qty
- tanggal_pengiriman
- status_pengiriman
- material_type
- invoice_path
- surat_jalan_path
- gambar_path
- created_at, updated_at

### customers / suppliers / users
- Tetap dipakai dengan penyesuaian nama domain jika diperlukan.

## Old -> New Mapping

### barang -> materials/products
- id_barang -> legacy_barang_id
- kode -> kode
- nama -> nama_material / nama_produk
- quantity -> stok_saat_ini (materials), qty default/reference untuk products (opsional)
- stok_minimum -> stok_minimum (materials)
- satuan -> satuan
- ukuran -> ukuran
- panjang, lebar, tinggi -> panjang, lebar, tinggi
- gambar_path -> gambar_path
- material_type -> material_type (materials + snapshot shipments)
- status=material -> materials
- status=produk -> products

### barang_masuk -> material_orders
- id_barang_masuk -> legacy_barang_masuk_id
- id_barang -> map by legacy_barang_id ke id_material
- id_supplier -> id_supplier
- id_user -> id_user
- quantity -> qty
- satuan -> satuan
- status -> status
- tanggal_masuk -> tgl_pemesanan
- estimasi_tiba -> estimasi_tiba
- invoice_path -> invoice_path
- surat_jalan_path -> surat_jalan_path
- gambar_path -> gambar_path

### barang_dalam_proses -> production_items
- id_barang_proses -> legacy_barang_proses_id
- id_barang -> map by legacy_barang_id ke id_produk
- id_barang_mentah -> map by legacy_barang_id ke id_material
- id_customer -> id_pelanggan
- id_user -> id_user
- quantity -> qty
- tanggal_selesai -> tgl_selesai
- created_at -> tgl_dibuat + created_at
- status_kirim/processing/reserve* -> tetap sama
- satuan/ukuran -> tetap

### barang_keluar -> shipments
- id_barang_keluar -> legacy_barang_keluar_id
- id_barang_proses -> id_barang_proses (setelah map production)
- id_barang -> id_produk (map by legacy)
- id_customer -> id_pelanggan
- id_user -> id_user
- quantity -> qty
- tanggal_keluar -> tanggal_pengiriman
- status_pengiriman -> status_pengiriman
- invoice_path/surat_jalan_path/gambar_path -> tetap
- material_type -> material_type

## Gap Analysis Summary
Field berikut wajib dipertahankan karena dipakai bisnis/controller/view:
- kode
- gambar_path
- ukuran
- satuan
- panjang, lebar, tinggi
- stok_minimum
- status_kirim
- processing/reserve_token/processing_started_at/processing_by
- status_pengiriman
- invoice_path, surat_jalan_path
- estimasi_tiba
- created_at, updated_at

## Role Access Baseline (Target)
- Admin: full CRUD.
- Supervisor: read-only dashboard, laporan, barang dalam proses, pengiriman.
- Supervisor forbidden (403) untuk:
  - data supplier
  - data pelanggan
  - data material
  - data produk
  - user management
  - semua create/edit/delete endpoint

## Notes
- Tabel legacy tetap dipertahankan selama fase transisi.
- Semua relasi custom key harus eksplisit (belongsTo foreign_key, owner_key).