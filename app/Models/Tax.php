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

    public function products()
    {
        return $this->hasMany(Product::class, 'TaxID', 'TaxID');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'TaxID', 'TaxID');
    }
}
