<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('created_by_id')->constrained(table: 'users')->onDelete('cascade');
            $table->foreignUuid('updated_by_id')->nullable()->constrained(table: 'users')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('telephone_number')->nullable();
            $table->string('address')->nullable();
            $table->fullText('address');
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_mobile_number')->nullable();
            $table->string('company_telephone_number')->nullable();
            $table->string('tax_identification_number')->nullable();
            $table->decimal('credit_limit', 20, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
