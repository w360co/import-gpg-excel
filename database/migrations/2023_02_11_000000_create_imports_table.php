<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        Schema::create('imports', function (Blueprint $table) {
            $table->unsignedBigInteger('id',true);
            $table->string('name')->index();
            $table->string('storage')->index();
            $table->string('report')->nullable();
            $table->string('author')->nullable();
            $table->unsignedBigInteger('total_rows')->default('0');
            $table->unsignedBigInteger('failed_rows')->default('0');
            $table->unsignedBigInteger('completed_rows')->default('0');
            $table->enum('state', ['pending', 'processing', 'completed','failed'])->default('pending');
            $table->string('model_type')->index();
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imports');
    }
}