<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    protected $table = 'combos';
    protected $primaryKey = 'combo_id';
    public $timestamps = false;

    protected $fillable = [
        'tour_id',
        'hotel_id',
        'transportation_id',
        'image',
        'price',
        'description',
        'is_deleted'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tour_id' => 'integer',
        'hotel_id' => 'integer',
        'transportation_id' => 'integer',
        'is_deleted' => 'string'
    ];

    protected $appends = ['formatted_price', 'total_original_price', 'discount_amount', 'discount_percent'];

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . ' VNĐ';
    }

    public function getTotalOriginalPriceAttribute()
    {
        $total = 0;
        if ($this->tour) $total += $this->tour->price ?? 0;
        if ($this->hotel) $total += $this->hotel->price ?? 0;
        if ($this->transportation) $total += $this->transportation->price ?? 0;
        return $total;
    }

    public function getDiscountAmountAttribute()
    {
        return $this->total_original_price - $this->price;
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->total_original_price > 0) {
            return round(($this->discount_amount / $this->total_original_price) * 100, 1);
        }
        return 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('is_deleted', 'inactive');
    }

    public function scopeWithTour($query)
    {
        return $query->whereNotNull('tour_id');
    }

    public function scopeWithHotel($query)
    {
        return $query->whereNotNull('hotel_id');
    }

    public function scopeWithTransportation($query)
    {
        return $query->whereNotNull('transportation_id');
    }

    // Relationships
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }

    public function transportation()
    {
        return $this->belongsTo(Transportation::class, 'transportation_id', 'transportation_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'combo_id', 'combo_id');
    }
}
