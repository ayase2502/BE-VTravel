<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    protected $primaryKey = 'guide_id';

    protected $fillable = [
        'name',
        'gender',
        'language',
        'experience_years',
        'album_id',
        'is_deleted',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }
}