<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('medical_nutrition_recommendations')) {
            return;
        }

        if (Schema::hasColumn('supervisor_notifications', 'medical_nutrition_recommendation_id')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        $this->dropForeignIfExists('supervisor_notifications', 'receiving_task_id');
        $this->dropIndexIfExists('supervisor_notifications', 'supervisor_notif_user_task_unique');
        $this->dropIndexIfExists('supervisor_notifications', 'supervisor_notifications_user_id_receiving_task_id_unique');

        if (Schema::hasColumn('supervisor_notifications', 'receiving_task_id')) {
            Schema::table('supervisor_notifications', function (Blueprint $table) {
                $table->dropColumn('receiving_task_id');
            });
        }

        Schema::table('supervisor_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('receiving_task_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('medical_nutrition_recommendation_id')->nullable()->after('receiving_task_id');
        });

        Schema::table('supervisor_notifications', function (Blueprint $table) {
            $table->foreign('receiving_task_id', 'sup_notif_task_fk')
                ->references('id')
                ->on('receiving_tasks')
                ->cascadeOnDelete();
            $table->foreign('medical_nutrition_recommendation_id', 'sup_notif_nutrition_fk')
                ->references('id')
                ->on('medical_nutrition_recommendations')
                ->cascadeOnDelete();
            $table->unique(['user_id', 'receiving_task_id'], 'supervisor_notifications_user_task_unique');
            $table->unique(['user_id', 'medical_nutrition_recommendation_id'], 'supervisor_notifications_user_nutrition_unique');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        if (! Schema::hasColumn('supervisor_notifications', 'medical_nutrition_recommendation_id')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::table('supervisor_notifications', function (Blueprint $table) {
            $table->dropUnique('supervisor_notifications_user_nutrition_unique');
            $table->dropUnique('supervisor_notifications_user_task_unique');
            $table->dropForeign(['medical_nutrition_recommendation_id']);
            $table->dropForeign(['receiving_task_id']);
            $table->dropColumn(['medical_nutrition_recommendation_id', 'receiving_task_id']);
        });

        Schema::table('supervisor_notifications', function (Blueprint $table) {
            $table->foreignId('receiving_task_id')
                ->after('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unique(['user_id', 'receiving_task_id'], 'supervisor_notif_user_task_unique');
        });

        Schema::enableForeignKeyConstraints();
    }

    private function dropForeignIfExists(string $table, string $column): void
    {
        $database = Schema::getConnection()->getDatabaseName();

        $constraint = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($constraint) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        }
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        $database = Schema::getConnection()->getDatabaseName();

        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
        }
    }
};
