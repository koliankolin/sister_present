<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $fillable = ['img', 'text', 'link', 'likes', 'dt_inst'];

    public function set(string $attr, $value) {
        if (!in_array($attr, $this->fillable)) {
            throw new \Exception('Invalid attribute');
        }

        $this->__set($attr, $value);
    }
}
