<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('client_name')->unique();
            $table->string('description');
            $table->string('email')->unique();
            $table->string('client_id')->unique();
            $table->string('ova')->unique();
            $table->string('password');
            $table->string('phone')->nullable(); // Contact person incase we get issues from the provider

            $table->string('created_by');

            $table->boolean('approved')->default(false);
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->boolean('blocked')->default(false);
            $table->string('blocked_by')->nullable();
            $table->timestamp('blocked_at')->nullable();
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
        Schema::dropIfExists('users');
    }
};
