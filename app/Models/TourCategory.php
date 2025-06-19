<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourCategory extends Model
{
    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name', 'thumbnail'];
}

