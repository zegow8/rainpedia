<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    protected $guarded = ['id'];
    const UPDATED_AT = null; // Cuma matiin updated_at, created_at tetep jalan
}