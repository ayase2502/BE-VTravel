<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomTour extends Model
{
    use HasFactory;

    protected $primaryKey = 'custom_tour_id';

    protected $fillable = [
        'user_id',
        'destination',
        'start_date',
        'end_date',
        'num_people',
        'note',
        'status',
        'is_deleted'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
