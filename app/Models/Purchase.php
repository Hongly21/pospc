<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';
    protected $primaryKey = 'PurchaseID';

    protected $fillable = [
        'SupplierID',  'Date', 'Total'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'PurchaseID');
    }
}
