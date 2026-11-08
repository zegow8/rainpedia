<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model {
    protected $guarded = ['id'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function borrowings() {
        return $this->hasMany(Borrowing::class);
    }

    public function activeBorrowings() {
        return $this->hasMany(Borrowing::class)->whereIn('status', ['Borrowed', 'Overdue'])->with('user');
    }
}
