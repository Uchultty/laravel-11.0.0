<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionItem extends Model
{
    use HasFactory;

    protected $table = 'barang_dalam_proses';
    protected $primaryKey = 'id_barang_proses';

    protected $fillable = [
        'legacy_barang_proses_id',
        'id_produk',
        'id_material',
        'id_pelanggan',
        'no_gambar',
        'no_po',
        'id_user',
        'qty',
        'satuan',
        'ukuran',
        'tgl_dibuat',
        'tgl_selesai',
        'status_kirim',
        'processing',
        'reserve_token',
        'processing_started_at',
        'processing_by',
    ];

    protected $casts = [
        'tgl_dibuat' => 'date',
        'tgl_selesai' => 'date',
        'status_kirim' => 'boolean',
        'processing' => 'boolean',
        'processing_started_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function produk()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_product');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Customer::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'id_barang_proses', 'id_barang_proses');
    }

    // Backward-compatible accessors for legacy field names (views use these)
    public function getIdBarangAttribute()
    {
        return $this->id_produk;
    }

    public function getIdBarangMentahAttribute()
    {
        return $this->id_material;
    }

    public function getIdCustomerAttribute()
    {
        return $this->id_pelanggan;
    }

    public function getQuantityAttribute()
    {
        return $this->qty;
    }

    public function getTanggalSelesaiAttribute()
    {
        return $this->tgl_selesai;
    }

    // Backward-compatible relationship aliases
    public function getBarangAttribute()
    {
        return $this->produk;
    }

    public function getBarangMentahAttribute()
    {
        return $this->material;
    }

    public function getCustomerAttribute()
    {
        return $this->pelanggan;
    }
}
