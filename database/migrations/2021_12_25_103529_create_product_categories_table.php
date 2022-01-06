<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('party_companies')->onDelete('cascade');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('product_categories');
    }
}
