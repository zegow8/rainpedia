<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'phone', 'address', 'role', 'is_active'];
    protected $hidden = ['password'];

    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function borrowings() {
        return $this->hasMany(Borrowing::class);
    }

    public function activeBorrowings() {
        return $this->hasMany(Borrowing::class)->whereIn('status', ['Borrowed', 'Overdue'])->with('book');
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function fines() {
        return $this->hasMany(Fine::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    public function chats() {
        return $this->hasMany(Chat::class);
    }
}
