<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materials';
    protected $primaryKey = 'id_material';

    protected $fillable = [
        'source_barang_id',
        'kode',
        'nama',
        'id_jenis_barang',
        'quantity',
        'stok_minimum',
        'material_type',
        'satuan',
        'ukuran',
        'gambar_path',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected function nama(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper($value),
        );
    }

    public function jenisBarang()
    {
        return $this->belongsTo(JenisBarang::class, 'id_jenis_barang', 'id_jenis_barang');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_material', 'id_material', 'id_product')
            ->withTimestamps();
    }
}
