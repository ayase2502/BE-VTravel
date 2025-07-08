<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $primaryKey = 'hotel_id';

    protected $fillable = [
        'name', 'location', 'room_type', 'price', 'description',
        'image', 'album_id', 'is_deleted'
    ];

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }
}
