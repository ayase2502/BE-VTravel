<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    protected $table = 'transportations'; // Đặt tên bảng tương ứng

    protected $primaryKey = 'transportation_id'; // Đặt khóa chính tương ứng

    // Nếu bảng không sử dụng timestamps (created_at, updated_at), thêm dòng này
    public $timestamps = false;

    // Định nghĩa các thuộc tính có thể gán giá trị đại chúng
    protected $fillable = [
        'type',
        'name',
        'price',
        'album_id',
        'is_deleted'
    ];

    /**
     * Scope để lấy danh sách vận chuyển đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 'active');
    }

    /**
     * Scope để lấy danh sách vận chuyển đã xóa
     */
    public function scopeInactive($query)
    {
        return $query->where('is_deleted', 'inactive');
    }

    /**
     * Scope để lọc theo loại phương tiện
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Thiết lập quan hệ với model Album
     */
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'album_id');
    }
}
