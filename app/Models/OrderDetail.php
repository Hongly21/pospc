<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'orderdetails';
    protected $primaryKey = 'OrderDetailID';

    protected $fillable = [
        'OrderID', 'ProductID', 'Quantity', 'Subtotal', 'TaxAmount', 'TaxRate'
    ];

    protected $casts = [
        'Quantity'  => 'integer',
        'Subtotal'  => 'float',
        'TaxAmount' => 'float',
        'TaxRate'   => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }

    public function getTaxRateAttribute($value)
    {
        if (! is_null($value) && $value !== '') {
            return (float) $value;
        }

        if ($this->TaxAmount !== null && $this->Subtotal !== null) {
            $baseTotal = $this->Subtotal - $this->TaxAmount;
            if ($baseTotal > 0) {
                return round(($this->TaxAmount / $baseTotal) * 100, 2);
            }
        }

        return 0;
    }

    public function getBaseAmountAttribute()
    {
        if ($this->Quantity > 0 && $this->TaxAmount !== null) {
            return round(($this->Subtotal - $this->TaxAmount) / $this->Quantity * $this->Quantity, 2);
        }

        return 0;
    }

    public function getTaxAmountAttribute($value)
    {
        if (! is_null($value) && $value !== '') {
            return round((float) $value, 2);
        }

        if ($this->TaxRate !== null && $this->Quantity > 0 && $this->Subtotal !== null) {
            $baseTotal = $this->Subtotal / (1 + ($this->TaxRate / 100));
            return round($this->Subtotal - $baseTotal, 2);
        }

        return 0;
    }

    public function getUnitPriceAttribute()
    {
        if ($this->Quantity > 0 && $this->TaxAmount !== null) {
            return round(($this->Subtotal - $this->TaxAmount) / $this->Quantity, 2);
        }

        return 0;
    }
}
