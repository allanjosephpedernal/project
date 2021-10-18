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
            $table->id();
            $table->string('name')->nullable()->default(NULL);
            $table->string('user_name',20)->nullable()->default(NULL);
            $table->string('avatar')->nullable()->default(NULL);
            $table->string('email')->unique()->nullable()->default(NULL);
            $table->timestamp('email_verified_at')->nullable()->default(NULL);
            $table->string('password')->nullable()->default(NULL);
            $table->string('pin',6)->nullable()->default(NULL);
            $table->rememberToken()->nullable()->default(NULL);
            $table->enum('user_role',['admin','user'])->nullable()->default('user');
            $table->datetime('registered_at')->nullable()->default(NULL);
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
        Schema::dropIfExists('users');
    }
}
