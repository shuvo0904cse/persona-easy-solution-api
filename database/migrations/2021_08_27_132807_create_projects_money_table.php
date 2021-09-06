<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects_money', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('phase_id')->index();
            $table->string('title')->index();
            $table->longText('description')->nullable();
            $table->double('amount', 15, 2)->nullable();
            $table->enum('type', ['INCOME', 'EXPENSE']);
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('projects_money', function($table) {
            $table->foreign('phase_id')->references('id')->on('projects_phase')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects_money');
    }
}
