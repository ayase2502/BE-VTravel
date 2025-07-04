<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'user_id',
        'booking_type',
        'related_id',
        'start_date',
        'end_date',
        'quantity',
        'total_price',
        'payment_method',
        'status',
        'cancel_reason',
        'is_deleted'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
