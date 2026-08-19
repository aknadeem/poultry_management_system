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
            $table->unsignedBigInteger('cb_number')->default(0);
            $table->string('cb_code')->unique()->nullable();
            $table->string('type')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0)->nullable();
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->decimal('dr', 15, 2)->default(0);
            $table->decimal('cr', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('status')->default('unpaid');
            $table->boolean('is_active')->default(1);
            $table->foreignId('company_id')->nullable()->constrained('party_companies')->onDelete('cascade');
            $table->string('balance_type')->nullable();  // i.e chick_purchase, feed_purchase
            $table->unsignedBigInteger('model_id')->nullable();
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
