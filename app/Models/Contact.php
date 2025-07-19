<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'contact_id';
    public $timestamps = false; // Vì chỉ có created_at

    protected $fillable = [
        'name',
        'email', 
        'message',
        'status',
        'is_deleted'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'status' => 'string',
        'is_deleted' => 'string'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('is_deleted', 'inactive');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status === 'new' ? 'Mới' : 'Đã xử lý';
    }

    public function getCreatedAtFormatAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : null;
    }
}
