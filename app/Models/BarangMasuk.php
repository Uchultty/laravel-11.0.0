<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';
    protected $primaryKey = 'id_barang_masuk';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_barang',
        'id_jenis_barang',
        'id_supplier',
        'id_user',
        'quantity',
        'satuan',
        'status',
        'tanggal_masuk',
        'estimasi_tiba',
        'gambar_path',
        'surat_jalan_path',
        'invoice_path',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'estimasi_tiba' => 'date',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function jenisBarang()
    {
        return $this->belongsTo(JenisBarang::class, 'id_jenis_barang', 'id_jenis_barang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    protected static function booted(): void
    {
        static::creating(function (BarangMasuk $barangMasuk) {
            if (empty($barangMasuk->id_barang_masuk)) {
                $barangMasuk->id_barang_masuk = static::generateIdBarangMasuk();
            }
        });
    }

    private static function generateIdBarangMasuk(): string
    {
        $lastId = static::query()->orderByDesc('id_barang_masuk')->value('id_barang_masuk');
        $nextNumber = 1;

        if ($lastId && preg_match('/^BMS(\d+)$/', $lastId, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'BMS' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
