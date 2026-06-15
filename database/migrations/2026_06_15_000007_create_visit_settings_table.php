<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_settings', function (Blueprint $table) {
            $table->id();
            $table->string('status_text')->nullable();
            $table->boolean('status_visible')->default(true);
            $table->text('urgent_alert')->nullable();
            $table->string('ambulance_phone')->nullable();
            $table->string('security_phone')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('entry_instructions')->nullable();
            $table->json('working_days')->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->string('last_ticket_time_note')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_settings');
    }
};
