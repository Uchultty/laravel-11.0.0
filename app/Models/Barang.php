<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'kode',
        'nama',
        'id_jenis_barang',
        'quantity',
        'satuan',
        'material_type',
        'stok_minimum',
        'ukuran',
        'status',
        'gambar_path',
    ];

    public function jenisBarang()
    {
        return $this->belongsTo(JenisBarang::class, 'id_jenis_barang', 'id_jenis_barang');
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_barang', 'id_barang');
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'id_barang', 'id_barang');
    }

    protected static function booted(): void
    {
        static::creating(function (Barang $barang) {
            if (empty($barang->id_barang)) {
                $barang->id_barang = static::generateIdBarang();
            }
        });
    }

    private static function generateIdBarang(): string
    {
        $lastId = static::query()->orderByDesc('id_barang')->value('id_barang');
        $nextNumber = 1;

        if ($lastId && preg_match('/^BRG(\d+)$/', $lastId, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'BRG' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
