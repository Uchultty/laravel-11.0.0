<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisBarang extends Model
{
    use HasFactory;

    protected $table = 'jenis_barang';
    protected $primaryKey = 'id_jenis_barang';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'id_jenis_barang', 'id_jenis_barang');
    }
}
