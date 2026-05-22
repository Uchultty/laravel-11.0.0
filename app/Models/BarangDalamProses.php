<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\User;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;

class BarangDalamProses extends Model
{
    use HasFactory;

    protected $table = 'barang_dalam_proses';

    protected $primaryKey = 'id_barang_proses';

    protected $fillable = [
        'id_barang',
        'id_barang_mentah',
        'id_user',
        'id_customer',
        'id_barang_keluar',
        'status_kirim',
        'quantity',
        'barang_mentah',
        'satuan',
        'ukuran',
        'tanggal_selesai',
        'processing',
        'reserve_token',
        'processing_started_at',
        'processing_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'tanggal_selesai' => 'date',
        'created_at' => 'datetime',
        'status_kirim' => 'boolean',
    ];

    public function getIsSiapDikirimAttribute(): bool
    {
        return (bool) $this->status_kirim && empty($this->id_barang_keluar);
    }

    public function getIsSelesaiAttribute(): bool
    {
        return ! empty($this->id_barang_keluar);
    }

    public function getStatusLifecycleAttribute(): string
    {
        if ($this->is_selesai) {
            return 'Selesai';
        }

        if ($this->is_siap_dikirim) {
            return 'Siap Dikirim';
        }

        return 'Diproses';
    }

    public function getStatusLifecycleClassAttribute(): string
    {
        if ($this->is_selesai) {
            return 'bg-slate-100 text-slate-700 border-slate-200';
        }

        if ($this->is_siap_dikirim) {
            return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        }

        return 'bg-amber-100 text-amber-700 border-amber-200';
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_pelanggan');
    }

    public function barangMentah()
    {
        return $this->belongsTo(Barang::class, 'id_barang_mentah', 'id_barang');
    }

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'id_barang_keluar', 'id_barang_keluar');
    }

    public function barangMasukTerbaru()
    {
        return $this->hasOne(BarangMasuk::class, 'id_barang', 'id_barang')->latestOfMany('created_at');
    }
}
