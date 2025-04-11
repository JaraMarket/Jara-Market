<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ingredient_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 8, 2)->default(1);
            $table->string('unit')->default('piece');
            $table->timestamps();
            
            $table->unique(['ingredient_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ingredient_product');
    }
}; 