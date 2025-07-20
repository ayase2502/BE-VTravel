<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationCategory extends Model
{
    protected $primaryKey = 'category_id';
    public $timestamps = false; // Nếu không có created_at, updated_at

    protected $fillable = [
        'category_name',
        'thumbnail',
        'is_deleted',
    ];

    protected $appends = ['thumbnail_url'];

    /**
     * Accessor: Trả về URL thumbnail đầy đủ
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    /**
     * Scope: Lọc theo active
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 'active');
    }

    /**
     * Scope: Lọc theo inactive
     */
    public function scopeInactive($query)
    {
        return $query->where('is_deleted', 'inactive');
    }

    /**
     * Quan hệ: Một category có nhiều destination
     */
    public function destinations()
    {
        return $this->hasMany(Destination::class, 'category_id', 'category_id');
    }

    /**
     * Quan hệ: Lấy destinations active
     */
    public function activeDestinations()
    {
        return $this->hasMany(Destination::class, 'category_id', 'category_id')
                    ->where('is_deleted', 'active');
    }

    /**
     * Quan hệ: Lấy tours thông qua destinations
     */
    public function tours()
    {
        return $this->hasManyThrough(Tour::class, Destination::class, 'category_id', 'destination_id', 'category_id', 'destination_id');
    }

    /**
     * Accessor: Đếm số destinations
     */
    public function getDestinationsCountAttribute()
    {
        return $this->destinations()->count();
    }

    /**
     * Accessor: Đếm số destinations active
     */
    public function getActiveDestinationsCountAttribute()
    {
        return $this->activeDestinations()->count();
    }
}
