<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $primaryKey = 'album_id';
    protected $fillable = ['album_name'];

    public function images(){
        return $this->hasMany(AlbumImage::class, 'album_id', 'album_id');
    }

}