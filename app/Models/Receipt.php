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
        'ReceiptNo',
        'PaymentMethod',
        'PaidAmount',
        'ChangeAmount',
        'CreatedAt'
    ];


    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID', 'OrderID');
    }
}
