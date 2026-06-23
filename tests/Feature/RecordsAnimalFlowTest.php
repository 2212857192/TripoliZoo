<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecordsAnimalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_officer_can_list_and_register_animal(): void
    {
        Storage::fake('public');

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/animals')
            ->assertOk()
            ->assertSee('الحيوانات داخل الحديقة فعلياً', false);

        $response = $this->actingAs($officer)->post('/records/animals', [
            'species' => 'غزال الريم',
            'name' => 'نور',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'age_method' => 'birth',
            'birth_date' => '2024-01-15',
            'origin' => 'وارد من خارج الحديقة',
            'animal_source' => 'سجل ورقي قديم',
            'photo' => UploadedFile::fake()->create('animal.jpg', 100),
        ]);

        $animal = Animal::withoutGlobalScopes()->where('name', 'نور')->first();
        $this->assertNotNull($animal);
        $response->assertRedirect(route('records.animals.show', $animal));

        $this->actingAs($officer)
            ->get('/records/animals')
            ->assertOk()
            ->assertSee('نور', false)
            ->assertSee($animal->code, false);

        $this->actingAs($officer)
            ->get('/records/animals/'.$animal->code)
            ->assertOk()
            ->assertSee($animal->code, false)
            ->assertSee('originInfo', false)
            ->assertSee('medical', false);
    }

    public function test_animal_show_displays_medical_history_from_field_case(): void
    {
        [$vet, $animal] = $this->seedAnimalForRecords();

        Sanctum::actingAs($vet);
        $this->postJson('/api/auth/doctor/field-cases', [
            'animal_code' => $animal->code,
            'open_reason' => 'عرج خفيف',
        ])->assertCreated();

        $fieldCase = \App\Models\FieldCase::query()->firstOrFail();
        $this->postJson('/api/auth/doctor/cases/field-'.$fieldCase->case_number.'/procedures', [
            'diagnosis' => 'التواء بسيط',
            'treatment' => 'راحة ومضاد التهاب',
            'case_result' => 'continue_treatment',
        ])->assertCreated();

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/animals/'.$animal->code)
            ->assertOk()
            ->assertSee($fieldCase->case_number, false)
            ->assertSee('diagnoses', false)
            ->assertSee('treatments', false);
    }

    public function test_records_officer_can_update_animal(): void
    {
        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G002',
            'species' => 'غزال الريم',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'age_method' => 'approx',
            'approx_age_value' => 3,
            'approx_age_unit' => 'سنوات',
            'origin' => 'مولود داخل الحديقة',
            'source' => 'records',
            'registration_note' => 'سجل قديم',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now()->toDateString(),
        ]);

        $this->actingAs($officer)
            ->put('/records/animals/'.$animal->code, [
                'species' => 'غزال أبيض',
                'name' => 'صخر',
                'gender' => 'ذكر',
                'age_method' => 'approx',
                'approx_age_value' => 4,
                'approx_age_unit' => 'سنوات',
                'origin' => 'مولود داخل الحديقة',
                'animal_source' => 'تحديث السجل',
            ])
            ->assertRedirect(route('records.animals.show', $animal));

        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'species' => 'غزال أبيض',
            'name' => 'صخر',
            'registration_note' => 'تحديث السجل',
        ]);
    }

    public function test_records_officer_can_document_animal_exit(): void
    {
        Storage::fake('public');

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G003',
            'species' => 'لاما',
            'group' => 'الدب واللامة',
            'gender' => 'أنثى',
            'age_method' => 'birth',
            'birth_date' => '2020-01-01',
            'origin' => 'وارد من خارج الحديقة',
            'source' => 'records',
            'registration_note' => 'وارد',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now()->toDateString(),
        ]);

        $this->actingAs($officer)
            ->post('/records/animals/'.$animal->code.'/exit', [
                'exit_date' => '2026-06-17',
                'exit_type' => 'transfer',
                'recipient' => 'حديقة طرابلس',
                'reason' => 'برنامج تبادل',
                'attachment' => UploadedFile::fake()->create('exit.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('records.animals.show', $animal));

        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'status' => AnimalStatus::Exited->value,
        ]);

        $this->assertDatabaseHas('animal_exits', [
            'animal_id' => $animal->id,
            'recipient' => 'حديقة طرابلس',
            'exit_type' => 'transfer',
        ]);

        $this->actingAs($officer)
            ->get('/records/animals/'.$animal->code)
            ->assertOk()
            ->assertSee('خرج هذا الحيوان من الحديقة', false)
            ->assertDontSee('id="btnEdit"', false)
            ->assertDontSee('id="btnExit"', false)
            ->assertDontSee('id="exitModal"', false);

        $this->actingAs($officer)
            ->get('/records/animals/'.$animal->code.'/edit')
            ->assertForbidden();

        $this->actingAs($officer)
            ->put('/records/animals/'.$animal->code, [
                'species' => 'لاما',
                'gender' => 'أنثى',
                'age_method' => 'birth',
                'birth_date' => '2020-01-01',
                'origin' => 'وارد من خارج الحديقة',
                'animal_source' => 'وارد',
            ])
            ->assertForbidden();

        $this->actingAs($officer)
            ->post('/records/animals/'.$animal->code.'/exit', [
                'exit_date' => '2026-06-18',
                'exit_type' => 'transfer',
                'recipient' => 'جهة أخرى',
                'reason' => 'محاولة ثانية',
            ])
            ->assertForbidden();
    }

    public function test_records_officer_can_export_animal_profile(): void
    {
        [$vet, $animal] = $this->seedAnimalForRecords();

        Sanctum::actingAs($vet);
        $this->postJson('/api/auth/doctor/field-cases', [
            'animal_code' => $animal->code,
            'open_reason' => 'فحص دوري',
        ])->assertCreated();

        $fieldCase = \App\Models\FieldCase::query()->firstOrFail();
        $this->postJson('/api/auth/doctor/cases/field-'.$fieldCase->case_number.'/procedures', [
            'diagnosis' => 'سليم',
            'treatment' => 'متابعة',
            'case_result' => 'continue_treatment',
        ])->assertCreated();

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/animals/'.$animal->code.'/export')
            ->assertOk()
            ->assertSee($animal->code, false)
            ->assertSee('جدول التشخيصات', false)
            ->assertSee('سليم', false)
            ->assertSee('المرفقات والتقارير', false);
    }

    public function test_active_animals_without_records_source_can_be_edited(): void
    {
        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G005',
            'species' => 'زebra',
            'group' => 'الخيول',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now()->toDateString(),
        ]);

        $this->actingAs($officer)
            ->get('/records/animals/'.$animal->code)
            ->assertOk()
            ->assertSee('records/animals/'.$animal->code.'/edit', false)
            ->assertSee('records/animals/'.$animal->code.'/exit', false);
    }

    /** @return array{0: User, 1: Animal} */
    private function seedAnimalForRecords(): array
    {
        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G001',
            'species' => 'غزال الريم',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'age_method' => 'approx',
            'approx_age_value' => 3,
            'approx_age_unit' => 'سنوات',
            'origin' => 'مولود داخل الحديقة',
            'source' => 'records',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now()->toDateString(),
        ]);

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'status' => 'active',
            'assigned_group' => 'الغزلان',
        ]);

        return [$vet, $animal];
    }

    public function test_registered_animal_appears_as_active_in_database(): void
    {
        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)->post('/records/animals', [
            'species' => 'أسد أفريقي',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'age_method' => 'approx',
            'approx_age_value' => 5,
            'approx_age_unit' => 'سنوات',
            'origin' => 'مولود داخل الحديقة',
            'animal_source' => 'مولود داخل الحديقة حسب السجلات الورقية',
        ]);

        $this->assertDatabaseHas('animals', [
            'species' => 'أسد أفريقي',
            'group' => 'القططية',
            'status' => AnimalStatus::Active->value,
            'source' => 'records',
        ]);
    }

    public function test_quarantine_animals_are_excluded_from_records_animals_list(): void
    {
        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'Q-500',
            'species' => 'زرافة',
            'group' => 'الثدييات الكبيرة',
            'gender' => 'أنثى',
            'source' => 'quarantine',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => now(),
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'PR-500',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'source' => 'quarantine',
            'status' => AnimalStatus::PendingReceipt->value,
            'registered_at' => now(),
        ]);

        $inside = Animal::withoutGlobalScopes()->create([
            'code' => 'IN-500',
            'species' => 'أسد',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'source' => 'records',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $this->actingAs($officer)
            ->get('/records/animals')
            ->assertOk()
            ->assertSee($inside->code, false)
            ->assertDontSee('Q-500', false)
            ->assertDontSee('PR-500', false);
    }

    public function test_active_quarantine_animal_without_receipt_is_excluded_from_animals_list(): void
    {
        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'Q-ACTIVE-BAD',
            'species' => 'زرافة',
            'group' => 'الثدييات الكبيرة',
            'gender' => 'أنثى',
            'source' => 'quarantine',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $official = Animal::withoutGlobalScopes()->create([
            'code' => 'IN-501',
            'species' => 'أسد',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'source' => 'records',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $this->actingAs($officer)
            ->get('/records/animals')
            ->assertOk()
            ->assertSee($official->code, false)
            ->assertDontSee('Q-ACTIVE-BAD', false);
    }

    public function test_quarantine_animal_profile_shows_quarantine_status_not_inside_zoo(): void
    {
        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'Q-600',
            'species' => 'زرافة',
            'group' => 'الثدييات الكبيرة',
            'gender' => 'أنثى',
            'source' => 'quarantine',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => now(),
        ]);

        $this->actingAs($officer)
            ->get('/records/animals/'.$animal->code)
            ->assertOk()
            ->assertSee("'quarantine'", false)
            ->assertSee('تحت الحجر الصحي', false)
            ->assertSee('لم يُسجَّل رسمياً داخل الحديقة', false)
            ->assertDontSee('id="btnEdit"', false);

        $this->actingAs($officer)
            ->get('/records/animals/'.$animal->code.'/edit')
            ->assertForbidden();
    }

    public function test_pending_receipt_animal_profile_is_locked_from_records_actions(): void
    {
        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'PR-600',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'source' => 'quarantine',
            'status' => AnimalStatus::PendingReceipt->value,
            'registered_at' => now(),
        ]);

        $this->actingAs($officer)
            ->get('/records/animals/'.$animal->code)
            ->assertOk()
            ->assertSee('بانتظار تأكيد الاستلام', false)
            ->assertDontSee('id="btnEdit"', false);

        $this->actingAs($officer)
            ->get('/records/animals/'.$animal->code.'/edit')
            ->assertForbidden();
    }
}
