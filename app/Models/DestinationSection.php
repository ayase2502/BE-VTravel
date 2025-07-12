<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DestinationSection extends Model
{
    use HasFactory;

    protected $table = 'destination_sections';

    protected $fillable = [
        'destination_id',
        'type',
        'title',
        'content',
    ];

    /**
     * Quan hệ: section thuộc về một điểm đến
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id', 'destination_id');
    }

    /**
     * Accessor: parse content nếu là json
     */
    public function getContentAttribute($value)
    {
        $jsonTypes = ['highlight', 'gallery', 'regionalDelicacies'];

        if (in_array($this->type, $jsonTypes)) {
            return json_decode($value, true);
        }

        return $value;
    }
}
