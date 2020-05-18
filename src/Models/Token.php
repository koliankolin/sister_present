<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Token extends Model {
    protected $table = 'tokens';
    protected $primaryKey = 'id';
    protected $fillable = ['token', 'type'];

    public function set(string $attr, $value) {
        if (!in_array($attr, $this->fillable)) {
            throw new \Exception('Invalid attribute');
        }

        $this->__set($attr, $value);
    }
}
