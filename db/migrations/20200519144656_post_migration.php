<?php

use \App\Migration\Migration;

class PostMigration extends Migration
{
    public function up()  {
        $this->schema->create('posts', function(Illuminate\Database\Schema\Blueprint $table){
            // Auto-increment id
            $table->increments('id');
            $table->string('img', 1500);
            $table->string('text', 3000)->nullable();
            $table->string('link', 1000);
            $table->integer('likes');
            $table->date('dt_inst');
            // Required for Eloquent's created_at and updated_at columns
            $table->timestamps();
        });
    }
    public function down()  {
        $this->schema->drop('tokens');
    }
}
