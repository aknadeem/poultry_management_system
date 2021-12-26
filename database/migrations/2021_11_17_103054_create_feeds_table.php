<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedsTable extends Migration
{

    public function up()
    {
        // Schema::dropIfExists('feeds');
        Schema::create('feeds', function (Blueprint $table) {
            $table->id();
            $table->string('feed_name')->unique();
            $table->string('feed_code')->unique();
            $table->foreignId('feed_category_id')->nullable()->constrained('feed_categories')->onDelete('cascade');
            $table->foreignId('feed_subcategory_id')->nullable()->constrained('feed_subcategories')->onDelete('cascade');
            $table->integer('total_quantity')->nullable();
            $table->integer('remaining_quantity')->nullable();
            $table->string('status')->nullable();
            $table->string('is_active')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feeds');
    }
}
