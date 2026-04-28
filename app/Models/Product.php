<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'ProductID';

    protected $fillable = [
        'Name',
        'CategoryID',
        'Brand',
        'Model',
        'CostPrice',
        'SellPrice',
        'Image',
        'Barcode',
        'Description',
        'WarrantyMonths',
        'Status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'CategoryID');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'ProductID');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'ProductID');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'ProductID', 'ProductID');
    }
}
