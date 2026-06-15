<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->string('species');
            $table->string('group');
            $table->string('gender');
            $table->string('distinguishing_marks')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('age_method')->nullable();
            $table->date('birth_date')->nullable();
            $table->unsignedSmallInteger('approx_age_value')->nullable();
            $table->string('approx_age_unit')->nullable();
            $table->string('origin')->nullable();
            $table->string('source')->nullable();
            $table->text('prior_history')->nullable();
            $table->string('prior_history_file')->nullable();
            $table->string('status')->default('active');
            $table->date('registered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
