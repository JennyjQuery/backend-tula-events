<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('name',100)->nullable(false);
            $table->string('place',100)->nullable(false);;
            $table->dateTime('date_from')->nullable(false);;
            $table->dateTime('date_to')->nullable(false);;
            $table->integer('type')->nullable(false);;
            $table->float('lat')->nullable(false);;
            $table->float('lon')->nullable(false);;
            $table->text('description')->nullable(false);;
            $table->string('image',255)->nullable(false);;
            $table->tinyInteger('autorization')->nullable(false);;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
