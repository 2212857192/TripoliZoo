<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /** @var list<array{name: string, code_prefix: string}> */
  private array $defaultGroups = [
    ['name' => 'القططية', 'code_prefix' => 'C'],
    ['name' => 'الطيور', 'code_prefix' => 'B'],
    ['name' => 'الزواحف', 'code_prefix' => 'R'],
    ['name' => 'القرود', 'code_prefix' => 'M'],
    ['name' => 'الغزلان', 'code_prefix' => 'G'],
    ['name' => 'الثدييات الكبيرة', 'code_prefix' => 'L'],
    ['name' => 'الثدييات الصغيرة', 'code_prefix' => 'S'],
    ['name' => 'الدب واللامة', 'code_prefix' => 'D'],
  ];

  public function up(): void
  {
    Schema::create('animal_groups', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique();
      $table->string('code_prefix', 10)->unique();
      $table->unsignedSmallInteger('sort_order')->default(0);
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });

    $now = now();

    foreach ($this->defaultGroups as $index => $group) {
      DB::table('animal_groups')->insert([
        'name' => $group['name'],
        'code_prefix' => $group['code_prefix'],
        'sort_order' => $index + 1,
        'is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
      ]);
    }

    Schema::table('animals', function (Blueprint $table) {
      $table->foreignId('animal_group_id')
        ->nullable()
        ->after('group')
        ->constrained('animal_groups')
        ->nullOnDelete();
    });

    Schema::table('users', function (Blueprint $table) {
      $table->foreignId('animal_group_id')
        ->nullable()
        ->after('assigned_group')
        ->constrained('animal_groups')
        ->nullOnDelete();
    });

    foreach (DB::table('animal_groups')->get() as $group) {
      DB::table('animals')
        ->where('group', $group->name)
        ->update(['animal_group_id' => $group->id]);

      DB::table('users')
        ->where('assigned_group', $group->name)
        ->update(['animal_group_id' => $group->id]);
    }
  }

  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropConstrainedForeignId('animal_group_id');
    });

    Schema::table('animals', function (Blueprint $table) {
      $table->dropConstrainedForeignId('animal_group_id');
    });

    Schema::dropIfExists('animal_groups');
  }
};
