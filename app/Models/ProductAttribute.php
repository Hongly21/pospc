<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'product_attributes';
    protected $primaryKey = 'AttributeID';

    protected $fillable = ['ProductID', 'AttributeName', 'AttributeValue'];
}
