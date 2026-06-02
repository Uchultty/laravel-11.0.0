<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialOrder extends Model
{
    use HasFactory;

    protected $table = 'pemesanan_material';
    protected $primaryKey = 'id_pemesanan';

    protected $fillable = [
        'legacy_barang_masuk_id',
        'id_material',
        'id_supplier',
        'id_user',
        'qty',
        'satuan',
        'status',
        'no_po',
        'tgl_pemesanan',
        'estimasi_tiba',
        'invoice_path',
        'surat_jalan_path',
        'gambar_path',
    ];

    protected $casts = [
        'tgl_pemesanan' => 'date',
        'estimasi_tiba' => 'date',
        'detail_materials' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
