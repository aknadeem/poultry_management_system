<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartyCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('party_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->onDelete('cascade');
            $table->string('company_name')->nullable();
            $table->string('company_code')->unique()->nullable();
            $table->string('nic_name')->nullable()->nullable();
            $table->foreignId('business_type_id')->constrained('business_types')->onDelete('cascade');
            $table->string('company_logo')->nullable();
            $table->string('company_noc')->nullable();
            $table->text('company_address')->nullable();
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('addedby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('party_companies');
    }
}
