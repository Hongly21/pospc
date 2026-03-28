<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'orderdetails';
    protected $primaryKey = 'OrderDetailID'; 

    protected $fillable = [
        'OrderID', 'ProductID', 'Quantity', 'Subtotal'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }
}
