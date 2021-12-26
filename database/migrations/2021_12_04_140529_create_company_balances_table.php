<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyBalancesTable extends Migration
{

    public function up()
    {
        Schema::create('company_balances', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->decimal('total_amount');
            $table->decimal('paid_amount')->default(0);
            $table->decimal('remaining_amount')->default(0);
            $table->decimal('dr')->default(0);
            $table->decimal('cr')->default(0);
            $table->decimal('balance')->default(0);
            $table->string('status')->default('unpaid');
            $table->boolean('is_active')->default(1);
            $table->foreignId('company_id')->nullable()->constrained('party_companies')->onDelete('cascade');
            $table->foreignId('chick_purchase_id')->nullable()->constrained('chick_purchases')->onDelete('cascade');
            $table->foreignId('feed_purchase_id')->nullable()->constrained('feeds')->onDelete('cascade');
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_balances');
    }
}
