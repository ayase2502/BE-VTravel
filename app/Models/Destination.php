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
        'album_id',
        'category_id',
        'description',
        'area',
        'img_banner',
        'category_id', // THÊM CATEGORY_ID
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
     * Quan hệ: Một destination thuộc về một category
     */
    public function category()
    {
        return $this->belongsTo(DestinationCategory::class, 'category_id', 'category_id');
    }

    /**
     * Quan hệ: Một destination có nhiều section
     */
    public function sections()
    {
        return $this->hasMany(DestinationSection::class, 'destination_id', 'destination_id');
    }

    /**
     * Quan hệ: Một destination có nhiều tour
     */
    public function tours()
    {
        return $this->hasMany(Tour::class, 'destination_id', 'destination_id');
    }

    /**
     * Accessor: Lấy tên category
     */
    public function getCategoryNameAttribute()
    {
        return $this->category ? $this->category->category_name : null;
    }
}
