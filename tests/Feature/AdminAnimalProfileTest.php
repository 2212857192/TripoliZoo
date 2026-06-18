<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\AnimalProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAnimalProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_filter_and_toggle_animal_profiles(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $visibleAnimal = Animal::withoutGlobalScopes()->create([
            'code' => 'ADM-001',
            'name' => 'ليو',
            'species' => 'أسد',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'source' => 'records',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $hiddenAnimal = Animal::withoutGlobalScopes()->create([
            'code' => 'ADM-002',
            'species' => 'زرافة',
            'group' => 'الثدييات الكبيرة',
            'gender' => 'أنثى',
            'source' => 'records',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $visibleProfile = AnimalProfile::create([
            'animal_id' => $visibleAnimal->id,
            'description' => 'محتوى تعريفي منشور للأسد في تطبيق الزائر.',
            'scientific_name' => 'Panthera leo',
            'display_code' => $visibleAnimal->code,
            'image_path' => 'animal-profiles/lion.jpg',
            'is_visible' => true,
            'created_by' => $admin->id,
        ]);

        AnimalProfile::create([
            'animal_id' => $hiddenAnimal->id,
            'description' => 'محتوى تعريفي مخفي عن الزوار حالياً في النظام.',
            'display_code' => $hiddenAnimal->code,
            'image_path' => 'animal-profiles/giraffe.jpg',
            'is_visible' => false,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get('/admin/animals?q=ليو')
            ->assertOk()
            ->assertSee('ليو', false)
            ->assertDontSee('ADM-002', false);

        $this->actingAs($admin)
            ->patch(route('admin.animals.visibility', $visibleProfile))
            ->assertRedirect();

        $this->assertFalse($visibleProfile->fresh()->is_visible);
    }

    public function test_admin_can_create_update_and_show_animal_profile(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'ADM-010',
            'name' => 'فهد',
            'species' => 'فهد',
            'group' => 'القططية',
            'gender' => 'أنثى',
            'source' => 'records',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post('/admin/animals', [
            'animal_id' => $animal->id,
            'description' => 'محتوى تعريفي جديد للفهد يظهر في تطبيق الزائر بعد النشر.',
            'image' => UploadedFile::fake()->create('cheetah.jpg', 100, 'image/jpeg'),
        ]);

        $profile = AnimalProfile::query()->first();
        $this->assertNotNull($profile);
        $response->assertRedirect(route('admin.animals.index'));

        $this->actingAs($admin)
            ->get(route('admin.animals.show', $profile))
            ->assertOk()
            ->assertSee('فهد', false)
            ->assertSee('القططية', false)
            ->assertSee('profile_id', false);

        $this->actingAs($admin)
            ->put(route('admin.animals.update', $profile), [
                'description' => 'وصف محدّث للفهد يظهر للزوار في التطبيق والخريطة مع بيانات الحيوان المختار.',
            ])
            ->assertRedirect(route('admin.animals.index'));

        $this->assertDatabaseHas('animal_profiles', [
            'id' => $profile->id,
            'description' => 'وصف محدّث للفهد يظهر للزوار في التطبيق والخريطة مع بيانات الحيوان المختار.',
        ]);
    }

    public function test_quarantine_animals_without_profile_do_not_appear_in_create_list(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'Q-ADM-01',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'source' => 'quarantine',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/animals/create')
            ->assertOk()
            ->assertSee('لا توجد حيوانات بدون محتوى تعريفي', false);
    }

    public function test_create_shows_validation_errors_for_short_description(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'ADM-020',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'source' => 'records',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $this->actingAs($admin)
            ->from('/admin/animals/create')
            ->post('/admin/animals', [
                'animal_id' => $animal->id,
                'description' => 'قصير',
                'image' => UploadedFile::fake()->create('gazelle.jpg', 100, 'image/jpeg'),
            ])
            ->assertRedirect('/admin/animals/create')
            ->assertSessionHasErrors('description');

        $this->assertDatabaseCount('animal_profiles', 0);
    }
}
