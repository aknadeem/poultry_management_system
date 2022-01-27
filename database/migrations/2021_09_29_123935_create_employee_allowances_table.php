<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeAllowancesTable extends Migration
{
    public function up()
    {
        Schema::create('employee_allowances', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('allowance_amount')->nullable();
            $table->boolean('is_Active')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_allowances');
    }
}
