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
            $table->string('name',255)->nullable(false);
            $table->string('place',255)->nullable(false);
            $table->string('geolocation',255)->nullable(false);
            $table->dateTime('date_from')->nullable(false);
            $table->dateTime('date_to')->nullable(false);
            $table->integer('type')->nullable(false);
            $table->float('lat')->nullable();
            $table->float('lon')->nullable();
            $table->text('description')->nullable(false);
            $table->string('image',255)->nullable();
            $table->tinyInteger('autorization')->nullable(false);
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
