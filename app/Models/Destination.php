<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Destination extends Model
{
    use HasFactory;

    protected $table = 'destinations';

    protected $primaryKey = 'destination_id';

    protected $fillable = [
        'name',
        'description',
        'location',
        'image',
        'album_id',
        'is_deleted'
    ];

    protected $appends = ['image_url'];

    // Lấy đúng URL ảnh công khai
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    // Quan hệ: Destination thuộc về Album
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'album_id');
    }
}
