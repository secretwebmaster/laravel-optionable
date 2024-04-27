<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('options', function(Blueprint $table){
            $table->id();
            $table->string('key');
            $table->string('value')->nullable();
            $table->unsignedBigInteger('optionable_id');
            $table->string('optionable_type');
            $table->timestamps();

            // $table->index('key');
            // $table->index('value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('options');
    }
}