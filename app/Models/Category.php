<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'CategoryID';

    protected $fillable = ['Name', 'status', 'TaxID'];

    public function products()
    {
        return $this->hasMany(Product::class, 'CategoryID', 'CategoryID');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'TaxID', 'TaxID');
    }
}
