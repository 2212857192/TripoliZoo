<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\AnimalProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorAnimalApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_visible_animal_profiles_are_returned_to_visitor_app(): void
    {
        $animal = Animal::create([
            'code' => 'C-001',
            'name' => 'ليو',
            'species' => 'الأسد الأفريقي',
            'group' => 'القططية',
            'gender' => 'male',
            'status' => 'active',
        ]);

        AnimalProfile::create([
            'animal_id' => $animal->id,
            'description' => 'محتوى تعريفي منشور للزائر عن الأسد الأفريقي.',
            'scientific_name' => 'Panthera leo',
            'display_code' => $animal->code,
            'image_path' => 'animal-profiles/lion.jpg',
            'is_visible' => true,
        ]);

        $hiddenAnimal = Animal::create([
            'code' => 'B-001',
            'name' => 'طائر مخفي',
            'species' => 'طائر',
            'group' => 'الطيور',
            'gender' => 'female',
            'status' => 'active',
        ]);

        AnimalProfile::create([
            'animal_id' => $hiddenAnimal->id,
            'description' => 'هذا المحتوى غير منشور للزوار.',
            'display_code' => $hiddenAnimal->code,
            'is_visible' => false,
        ]);

        $this->getJson('/api/animals')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'ليو')
            ->assertJsonPath('data.0.sci_name', 'الأسد الأفريقي')
            ->assertJsonPath('data.0.category', 'predators')
            ->assertJsonPath('data.0.qr_code', 'C-001')
            ->assertJsonStructure(['data' => [['qr_payload' => ['profile_id', 'animal_code']]]]);
    }

    public function test_animal_profile_can_be_loaded_by_animal_code(): void
    {
        $animal = Animal::create([
            'code' => 'B-007',
            'species' => 'فلامنغو',
            'group' => 'الطيور',
            'gender' => 'female',
            'status' => 'active',
        ]);

        AnimalProfile::create([
            'animal_id' => $animal->id,
            'description' => 'محتوى تعريفي منشور للزائر عن طائر الفلامنغو.',
            'scientific_name' => 'Phoenicopterus',
            'display_code' => $animal->code,
            'is_visible' => true,
        ]);

        $this->getJson('/api/animals/B-007')
            ->assertOk()
            ->assertJsonPath('data.name', 'فلامنغو')
            ->assertJsonPath('data.sci_name', 'الطيور')
            ->assertJsonPath('data.category', 'birds');
    }
}
