<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model {
    protected $guarded = ['id'];
    public $timestamps = false; // WAJIB: Karena gak ada updated_at
    public function book() { return $this->belongsTo(Book::class); }
    public function cart() { return $this->belongsTo(Cart::class); }
}