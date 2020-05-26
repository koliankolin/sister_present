<?php

use \App\Migration\Migration;

class TokenMigration extends Migration
{
    public function up()  {
        $this->schema->create('tokens', function(Illuminate\Database\Schema\Blueprint $table){
            // Auto-increment id
            $table->increments('id');
            $table->string('token', 300);
            $table->string('type');
            // Required for Eloquent's created_at and updated_at columns
            $table->timestamps();
        });
    }

    public function down()  {
        $this->schema->drop('tokens');
    }
}
