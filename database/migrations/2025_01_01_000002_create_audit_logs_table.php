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
        Schema::create('modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('created_by_id')->constrained(table: 'users')->onDelete('cascade');
            $table->foreignUuid('updated_by_id')->nullable()->constrained(table: 'users')->onDelete('cascade');
            $table->string('module');
            $table->foreign('module')->references('name')->on('modules');
            $table->enum('action', ['Create', 'Read', 'Update', 'Delete', 'Restore', 'Force Delete']);
            $table->text('data')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('modules');
        Schema::dropIfExists('audit_logs');
    }
};
