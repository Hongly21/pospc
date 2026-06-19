<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'taxes';
    protected $primaryKey = 'TaxID';

    protected $fillable = [
        'Name',
        'Rate',
        'Description',
        'Status',
    ];

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'TaxID', 'TaxID');
    }
}
