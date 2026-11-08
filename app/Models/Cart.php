<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model {
    protected $guarded = ['id'];
    // Biarkan timestamps menyala (default)
    public function details() { return $this->hasMany(CartDetail::class); }
}