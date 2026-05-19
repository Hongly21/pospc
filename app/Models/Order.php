<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'OrderID';

    protected $fillable = [
        'UserID',
        'CustomerID',
        'TotalAmount',
        'TotalTax',
        'PaymentType',
        'Status',
        'OrderDate'
    ];

    protected $casts = [
        'TotalAmount' => 'float',
        'TotalTax'    => 'float',
    ];

    protected $dates = [
        'OrderDate'
    ];


    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'OrderID', 'OrderID');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'OrderID', 'OrderID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'OrderID', 'OrderID');
    }
}
