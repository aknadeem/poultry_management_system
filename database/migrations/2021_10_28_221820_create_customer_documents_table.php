<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('customer_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('picture');
            $table->string('type')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_documents');
    }
}
