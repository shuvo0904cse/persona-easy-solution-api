<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserGroceriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_groceries', function (Blueprint $table) {
            $table->uuid('user_grocery_group_id')->index();
            $table->uuid('grocery_id')->index();
            $table->string('amount')->default(1);
            $table->string('unit')->default("kg");
        });

        Schema::table('user_groceries', function($table) {
            $table->foreign('user_grocery_group_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('grocery_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_groceries');
    }
}
