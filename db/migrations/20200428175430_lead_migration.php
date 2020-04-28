<?php

use \App\Migration\Migration;

class LeadMigration extends Migration
{
    public function up()  {
        $this->schema->create('leads', function(Illuminate\Database\Schema\Blueprint $table){
            // Auto-increment id
            $table->increments('id');
            $table->integer('phone');
            $table->string('name');
            $table->string('session_type');
            // Required for Eloquent's created_at and updated_at columns
            $table->timestamps();
        });
    }
    public function down()  {
        $this->schema->drop('leads');
    }
}
