<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ProductionItem;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';

    protected $primaryKey = 'id_pelanggan';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'nama',
        'pic',
        'alamat',
        'kontak',
        'email',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_pelanggan';
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'id_customer', 'id_pelanggan');
    }

    public function productionItems()
    {
        return $this->hasMany(ProductionItem::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function getIdCustomerAttribute()
    {
        return $this->id_pelanggan;
    }

    public function setIdCustomerAttribute($value): void
    {
        $this->attributes['id_pelanggan'] = $value;
    }
}
