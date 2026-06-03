<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_barang';
    protected $primaryKey = 'id_pengiriman';

    protected $fillable = [
        'legacy_barang_keluar_id',
        'id_barang_proses',
        'id_produk',
        'id_pelanggan',
        'no_po',
        'no_gambar',
        'id_user',
        'qty',
        'tanggal_pengiriman',
        'status_pengiriman',
        'material_type',
        'invoice_path',
        'surat_jalan_path',
        'gambar_path',
        'items',
    ];

    protected $casts = [
        'tanggal_pengiriman' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'items' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_pengiriman';
    }

    public function productionItem()
    {
        return $this->belongsTo(ProductionItem::class, 'id_barang_proses', 'id_barang_proses');
    }

    public function produk()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_product');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Customer::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function barang()
    {
        return $this->produk();
    }

    public function customer()
    {
        return $this->pelanggan();
    }

    public function getIdBarangKeluarAttribute()
    {
        return $this->id_pengiriman;
    }

    public function getIdBarangAttribute()
    {
        return $this->id_produk;
    }

    public function getIdCustomerAttribute()
    {
        return $this->id_pelanggan;
    }

    public function getQuantityAttribute()
    {
        return $this->qty;
    }

    public function getTanggalKeluarAttribute()
    {
        return $this->tanggal_pengiriman;
    }

    public function getStatusDisplayAttribute(): string
    {
        $statusLabels = [
            'Menunggu Pengiriman' => 'Dalam Proses',
            'Dalam Pengiriman' => 'Siap Dikirim',
        ];

        return $statusLabels[$this->status_pengiriman ?? ''] ?? ($this->status_pengiriman ?? 'Dalam Proses');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status_display) {
            'Siap Dikirim' => 'bg-blue-100 text-blue-800',
            'Sedang Dikirim' => 'bg-emerald-100 text-emerald-800',
            'Selesai' => 'bg-slate-100 text-slate-800',
            default => 'bg-amber-100 text-amber-800',
        };
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status_pengiriman', ['Siap Dikirim', 'Dalam Pengiriman', 'Sedang Dikirim']);
    }
}
