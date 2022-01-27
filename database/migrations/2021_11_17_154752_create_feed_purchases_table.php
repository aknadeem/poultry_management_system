<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedPurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('feed_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->foreignId('feed_id')->nullable()->constrained('feeds')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('party_companies')->onDelete('cascade');
            $table->date('purchase_date')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('remaining_quantity')->nullable();
            $table->decimal('price')->nullable();
            $table->decimal('discount_amount')->nullable();
            $table->decimal('per_bag_discount_amount')->nullable();
            $table->float('discount_percentage')->nullable();
            $table->decimal('total_price')->nullable();
            $table->decimal('final_price')->nullable();
            $table->string('bilty_number')->nullable();
            $table->decimal('bilty_charges')->nullable();
            $table->string('sale_order_number')->nullable();
            $table->string('delivery_order_number')->nullable();
            $table->text('description')->nullable();
            $table->string('picture')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feed_purchases');
    }
}
