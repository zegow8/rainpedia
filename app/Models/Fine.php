<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model {
    protected $guarded = ['id'];
    const UPDATED_AT = null; // Cuma matiin updated_at
    public function borrowing() { return $this->belongsTo(Borrowing::class); }
}