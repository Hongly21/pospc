<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use HasFactory;

    protected $table = 'inventory_adjustments';
    protected $primaryKey = 'AdjustmentID';

    protected $fillable = [
        'ProductID',
        'UserID',
        'Action',
        'Quantity',
        'Reason'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
