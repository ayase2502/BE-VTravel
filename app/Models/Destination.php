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
        'area',
        'img_banner',
        'is_deleted',
    ];

    protected $appends = ['img_banner_url'];

    /**
     * Accessor: Trả về URL ảnh banner đầy đủ
     */
    public function getImgBannerUrlAttribute()
    {
        return $this->img_banner ? asset('storage/' . $this->img_banner) : null;
    }

    /**
     * Scope: Lọc theo active
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 'active');
    }

    /**
     * Quan hệ: Một điểm đến có nhiều section
     */
    public function sections()
    {
        return $this->hasMany(DestinationSection::class, 'destination_id', 'destination_id');
    }
    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'tour_destinations', 'destination_id', 'tour_id');
    }
}
