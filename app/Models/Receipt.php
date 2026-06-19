<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'receipts';
    protected $primaryKey = 'ReceiptID';

    public $timestamps = false;

    protected $fillable = [
        'OrderID',
        'TaxID',
        'TaxRate',
        'TaxAmount',
        'ReceiptNo',
        'PaymentMethod',
        'PaidAmount',
        'ChangeAmount',
        'CreatedAt'
    ];

    protected $casts = [
        'TaxRate' => 'float',
        'TaxAmount' => 'float',
        'PaidAmount' => 'float',
        'ChangeAmount' => 'float',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID', 'OrderID');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'TaxID', 'TaxID');
    }
}
