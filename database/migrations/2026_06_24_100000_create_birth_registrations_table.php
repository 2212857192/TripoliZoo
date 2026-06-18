<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birth_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->foreignId('mother_id')->constrained('animals')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->string('group');
            $table->date('birth_date');
            $table->unsignedTinyInteger('birth_count');
            $table->timestamps();
        });

        Schema::create('birth_registration_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('birth_registration_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'birth_registration_id'], 'birth_reg_notif_user_reg_unique');
        });

        Schema::table('animals', function (Blueprint $table) {
            $table->foreignId('mother_id')->nullable()->after('registered_at')->constrained('animals')->nullOnDelete();
            $table->foreignId('birth_registration_id')->nullable()->after('mother_id')->constrained()->nullOnDelete();
            $table->text('registration_note')->nullable()->after('birth_registration_id');
        });
    }

    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('birth_registration_id');
            $table->dropConstrainedForeignId('mother_id');
            $table->dropColumn('registration_note');
        });

        Schema::dropIfExists('birth_registration_notifications');
        Schema::dropIfExists('birth_registrations');
    }
};
