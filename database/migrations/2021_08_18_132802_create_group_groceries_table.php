<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupGroceriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_groceries', function (Blueprint $table) {
            $table->uuid('user_grocery_group_id')->index();
            $table->uuid('grocery_id')->index();
            $table->string('amount')->default(1);
            $table->string('unit')->default("kg");
        });

        Schema::table('group_groceries', function($table) {
           // $table->foreign('user_grocery_group_id')->references('id')->on('user_grocery_groups')->onDelete('cascade');
         //   $table->foreign('grocery_id')->references('id')->on('groceries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_groceries');
    }
}
