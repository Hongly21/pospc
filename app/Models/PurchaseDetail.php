<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = 'purchasedetails';
    protected $primaryKey = 'PurchaseDetailID';

    protected $fillable = [
        'PurchaseID',
        'ProductID',
        'Qty',
        'CostPrice'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'PurchaseID', 'PurchaseID');
    }
}
