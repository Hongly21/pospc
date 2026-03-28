<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $primaryKey = 'InventoryID';

    public $timestamps = false;

    protected $fillable = [
        'ProductID',
        'Quantity',
        'ReorderLevel',
        'created_at',
        'updated_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }
}
