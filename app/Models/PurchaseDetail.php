<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = 'purchasedetails';
    protected $primaryKey = 'PurchaseDetailID';

    public $timestamps = false;

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
}
