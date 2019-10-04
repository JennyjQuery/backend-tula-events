<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',100)->nullable(false);
            $table->string('surname',100)->nullable(false);
            $table->date('date_of_birth');
            $table->string('phone',100)->unique()->nullable(false);
            $table->string('email')->unique()->nullable(false);
            $table->timestamp('password_verified_at')->nullable(false);
            $table->string('password',100)->nullable(false);
            $table->string('name_organization',100);
            $table->tinyInteger('sex');
            $table->string('avatar');
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
