<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Chỉ admin được xem danh sách user
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Admin hoặc chính chủ user mới được xem
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === 'admin' || $user->id === $model->id;
    }

    /**
     * Chỉ admin được thêm user
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Chỉ admin hoặc chính chủ mới được sửa
     */
    public function update(User $user, User $model): bool
    {
        return $user->role === 'admin' || $user->id === $model->id;
    }

    /**
     * Chỉ admin được xoá
     */
    public function delete(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }
}
