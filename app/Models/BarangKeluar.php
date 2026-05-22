<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar';
    protected $primaryKey = 'id_barang_keluar';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_barang',
        'id_customer',
        'id_user',
        'id_barang_proses',
        'quantity',
        'tanggal_keluar',
        'gambar_path',
        'surat_jalan_path',
        'invoice_path',
        'status_pengiriman',
        'material_type',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_pelanggan');
    }

    public function barangDalamProses()
    {
        return $this->belongsTo(BarangDalamProses::class, 'id_barang_proses', 'id_barang_proses');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function scopeActive($query)
    {
        return $query->where('status_pengiriman', '!=', 'Selesai');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status_pengiriman', 'Selesai');
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

    protected static function booted(): void
    {
        static::creating(function (BarangKeluar $barangKeluar) {
            if (empty($barangKeluar->id_barang_keluar)) {
                $barangKeluar->id_barang_keluar = static::generateIdBarangKeluar();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'id_barang_keluar';
    }

    private static function generateIdBarangKeluar(): string
    {
        $lastId = static::query()->orderByDesc('id_barang_keluar')->value('id_barang_keluar');
        $nextNumber = 1;

        if ($lastId && preg_match('/^BKL(\d+)$/', $lastId, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'BKL' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
