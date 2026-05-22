<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_supplier';

    protected $fillable = [
        'nama',
        'jabatan',
        'alamat',
        'kontak',
        'email',
    ];

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_supplier', 'id_supplier');
    }
}
