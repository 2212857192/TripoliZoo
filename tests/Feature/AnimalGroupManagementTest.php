<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\AnimalGroup;
use App\Models\User;
use App\Services\AnimalCodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimalGroupManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_seeds_default_groups_with_ids(): void
    {
        $this->assertCount(8, AnimalGroup::query()->get());
        $this->assertSame('C', AnimalGroup::query()->where('name', 'القططية')->value('code_prefix'));
    }

    public function test_admin_can_list_animal_groups(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get('/admin/animal-groups');

        $response->assertOk();
        $response->assertSee('المجموعات الحيوانية');
        $response->assertSee('القططية');
    }

    public function test_admin_can_create_animal_group(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post('/admin/animal-groups', [
            'name' => 'الخيول',
            'code_prefix' => 'H',
            'sort_order' => 99,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/animal-groups');
        $this->assertDatabaseHas('animal_groups', [
            'name' => 'الخيول',
            'code_prefix' => 'H',
        ]);
    }

    public function test_animal_syncs_group_name_from_animal_group_id(): void
    {
        $group = AnimalGroup::query()->where('name', 'القرود')->firstOrFail();

        $animal = Animal::create([
            'code' => 'M099',
            'species' => 'قرد',
            'animal_group_id' => $group->id,
            'gender' => 'ذكر',
            'status' => 'active',
        ]);

        $this->assertSame('القرود', $animal->fresh()->group);
    }

    public function test_api_returns_animal_groups_with_ids(): void
    {
        $response = $this->getJson('/api/animal-groups');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                ['id', 'name', 'code_prefix', 'sort_order'],
            ],
        ]);
    }

    public function test_new_group_is_available_across_system_helpers(): void
    {
        AnimalGroup::query()->create([
            'name' => 'الخيول',
            'code_prefix' => 'H',
            'sort_order' => 99,
            'is_active' => true,
        ]);

        $this->assertContains('الخيول', animal_groups());
        $this->assertSame('H', animal_group_prefixes()['الخيول']);
        $this->assertSame('H001', app(AnimalCodeGenerator::class)->nextForGroup('الخيول'));
    }

    public function test_records_officer_can_register_animal_in_new_group(): void
    {
        AnimalGroup::query()->create([
            'name' => 'الخيول',
            'code_prefix' => 'H',
            'sort_order' => 99,
            'is_active' => true,
        ]);

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $response = $this->actingAs($officer)->post('/records/animals', [
            'species' => 'حصان عربي',
            'group' => 'الخيول',
            'gender' => 'ذكر',
            'age_method' => 'approx',
            'approx_age_value' => 3,
            'approx_age_unit' => 'سنوات',
            'origin' => 'وارد من خارج الحديقة',
            'animal_source' => 'شراء من مزارع محلية',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('animals', [
            'species' => 'حصان عربي',
            'group' => 'الخيول',
            'code' => 'H001',
        ]);
        $this->assertNotNull(
            Animal::query()->where('group', 'الخيول')->value('animal_group_id'),
        );
    }

    public function test_registered_animals_count_includes_quarantine_and_group_name_links(): void
    {
        $group = AnimalGroup::query()->where('name', 'القططية')->firstOrFail();

        Animal::query()->create([
            'code' => 'C010',
            'species' => 'أسد',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'status' => 'active',
        ]);

        Animal::query()->create([
            'code' => 'Q010',
            'species' => 'قرد',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'status' => 'quarantine',
        ]);

        $listed = AnimalGroup::queryForAdminIndex()
            ->whereKey($group->id)
            ->firstOrFail();

        $this->assertSame(2, (int) $listed->registered_animals_count);
        $this->assertNotNull(Animal::query()->withQuarantine()->where('code', 'C010')->value('animal_group_id'));
    }

    public function test_linked_employees_count_uses_assigned_group_name(): void
    {
        $group = AnimalGroup::query()->where('name', 'الغزلان')->firstOrFail();

        User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'status' => 'active',
            'assigned_group' => 'الغزلان',
        ]);

        $listed = AnimalGroup::queryForAdminIndex()
            ->whereKey($group->id)
            ->firstOrFail();

        $this->assertSame(1, (int) $listed->linked_employees_count);
        $this->assertNotNull(
            User::query()->where('assigned_group', 'الغزلان')->value('animal_group_id'),
        );
    }
}
