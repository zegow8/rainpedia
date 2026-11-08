<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    protected $guarded = ['id'];
    const UPDATED_AT = null; // Cuma matiin updated_at
}