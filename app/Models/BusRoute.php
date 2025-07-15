<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusRoute extends Model
{
    protected $primaryKey = 'route_id';
    protected $fillable = [
        'route_name', 'vehicle_type', 'price', 'seats', 'album_id', 'is_deleted'
    ];

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }
}
