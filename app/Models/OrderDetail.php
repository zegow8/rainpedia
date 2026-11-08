<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model {
    protected $guarded = ['id'];
    public $timestamps = false; // WAJIB: Gak ada created_at/updated_at
    public function book() { return $this->belongsTo(Book::class); }
}