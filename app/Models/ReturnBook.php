<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ReturnBook extends Model {
    protected $table = 'returns';
    protected $guarded = ['id'];
    public $timestamps = false; // WAJIB dimatiin total
    public function borrowing() { return $this->belongsTo(Borrowing::class); }
}