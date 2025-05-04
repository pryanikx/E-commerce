<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 30);
            $table->string('article', length: 20)->unique();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('manufacturer_id');
            $table->text('description');
            $table->date('release_date');
            $table->decimal('price', total: 8, places: 2)->unsigned();
            $table->string('image_path');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
