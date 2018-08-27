<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_versions', function (Blueprint $table) {
            $table->uuid('id');
            $table->boolean('shouldExist')->default(true);
            $table->boolean('exists')->default(false);
            $table->jsonb('properties')->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->uuid('point_id');
            $table->uuid('user_id')->nullable();
            $table->timestamps();

            $table->primary('id');
            $table->foreign('point_id')->references('id')->on('points')
                ->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_versions');
    }
}
