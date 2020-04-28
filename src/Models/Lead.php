<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model {
    protected $table = 'leads';
    protected $primaryKey = 'id';
    protected $fillable = ['phone', 'name', 'session_type'];
}
