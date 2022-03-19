<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_number')->default(0);
            $table->string('expense_code')->unique()->nullable();
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->onDelete('cascade');
            $table->decimal('amount')->nullable();
            $table->date('expense_date')->nullable();
            $table->text('remarks')->nullable();
            $table->string('picture')->nullable();
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
