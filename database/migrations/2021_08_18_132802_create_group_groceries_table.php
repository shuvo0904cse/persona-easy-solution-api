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
            $table->uuid('group_id')->index();
            $table->uuid('grocery_id')->index();
            $table->string('amount')->nullable()->default(1);
            $table->string('unit')->nullable()->default("kg");
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
