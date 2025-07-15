<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motorbike extends Model
{
    use HasFactory;

    protected $primaryKey = 'bike_id';
    public $timestamps = false;

    protected $fillable = [
        'bike_type',
        'price_per_day',
        'location',
        'album_id',
        'is_deleted',
    ];

    // Quan hệ với album
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'album_id');
    }
}
