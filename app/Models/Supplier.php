<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $primaryKey = 'SupplierID';

    protected $fillable = [
        'Name',
        'Contact',
        'Address',
        'status'
    ];
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'SupplierID', 'SupplierID');
    }
}
