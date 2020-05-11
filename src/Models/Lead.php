<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model {
    protected $table = 'leads';
    protected $primaryKey = 'id';
    protected $fillable = ['phone', 'name', 'session_type'];

    public function set(string $attr, $value) {
        if (!in_array($attr, $this->fillable)) {
            throw new \Exception('Invalid attribute');
        }

        $this->__set($attr, $value);
    }


}
