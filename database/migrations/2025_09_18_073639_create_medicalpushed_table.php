<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medicalpushed', function (Blueprint $table) {
            $table->id();
            $table->string('reg_number');
            $table->string('firstnames')->nullable();
            $table->string('surname')->nullable();
            $table->date('dob')->nullable();
            $table->string('mobile')->nullable();
            $table->string('nationalid')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->string('payment_status')->nullable();
            $table->date('payment_date')->nullable();
            $table->json('api_response')->nullable(); // Store API responses
            $table->string('status')->default('pending'); // pending, success, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medicalpushed');
    }
};
