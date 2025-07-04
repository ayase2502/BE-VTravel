<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $primaryKey = 'tour_id';
    protected $fillable = [
        'category_id', 'album_id', 'tour_name', 'description', 'itinerary',
        'image', 'price', 'discount_price', 'destination', 'duration', 'status','is_deleted',
    ];

    public function category()
    {
        return $this->belongsTo(TourCategory::class, 'category_id');
    }

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'album_id');
    }
    
}
