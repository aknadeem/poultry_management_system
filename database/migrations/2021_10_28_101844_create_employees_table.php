<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        // Schema::dropIfExists('employees');
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('general_type')->nullable();
            $table->string('name')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('other_number')->nullable();
            $table->string('email')->nullable();
            $table->string('cnic_no')->nullable();
            $table->decimal('basic_salary')->default(0);
            $table->decimal('other_amount')->default(0);
            $table->decimal('net_salary')->nullable(0);
            $table->string('contract_period')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('employee_age')->nullable();
            $table->date('joining_date')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_police_record')->default(0);
            $table->text('address')->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('employee_image')->nullable();
            $table->string('employee_signature')->nullable();
            
            $table->foreignId('personal_farm_id')->nullable()->constrained('personal_farms')->onDelete('cascade');

            $table->foreignId('employee_type_id')->nullable()->constrained('employee_types')->onDelete('cascade');
            $table->foreignId('employee_level_id')->nullable()->constrained('employee_levels')->onDelete('cascade');
            $table->foreignId('employee_allowance_id')->nullable()->constrained('employee_allowances')->onDelete('cascade');
            
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');

            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
