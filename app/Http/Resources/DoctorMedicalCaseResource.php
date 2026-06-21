<?php

namespace App\Http\Resources;

use App\Enums\FieldCaseStatus;
use App\Enums\HospitalCaseStatus;
use App\Models\FieldCase;
use App\Models\HospitalCase;
use App\Models\MedicalCaseProcedure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin FieldCase|HospitalCase */
class DoctorMedicalCaseResource extends JsonResource
{
    /** @param  'field'|'hospital'  $caseType */
    public function __construct($resource, private string $caseType)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return $this->caseType === 'field'
            ? $this->fieldCaseArray()
            : $this->hospitalCaseArray();
    }

    /** @return array<string, mixed> */
    private function fieldCaseArray(): array
    {
        /** @var FieldCase $case */
        $case = $this->resource;
        $animal = $case->animal;
        $isActive = $case->status === FieldCaseStatus::Active;

        return [
            'id' => 'field-'.$case->case_number,
            'case_number' => $case->case_number,
            'case_type' => 'field',
            'status' => $isActive ? 'active' : 'closed',
            'field_status' => $case->status->value,
            'status_label' => $case->status->label(),
            'animal_id' => $animal?->code ?? '',
            'animal_type' => $animal?->species ?? '',
            'animal_group' => $case->group,
            'animal_gender' => $animal?->gender,
            'animal_age' => $animal?->formattedAge(),
            'open_reason' => $case->open_reason,
            'initial_note' => $case->initial_note,
            'closing_note' => $case->closing_note,
            'opened_at' => $case->opened_at?->toIso8601String(),
            'updated_at' => ($case->closed_at ?? $case->updated_at)?->toIso8601String(),
            'source_label' => $case->health_report_id ? 'من بلاغ صحي' : 'فتح يدوي',
            'can_register_procedure' => $isActive,
            'can_close' => $isActive,
            'procedures' => $this->mapProcedures($case),
            'nutrition_recommendations' => $this->mapNutritionRecommendations($case),
        ];
    }

    /** @return array<string, mixed> */
    private function hospitalCaseArray(): array
    {
        /** @var HospitalCase $case */
        $case = $this->resource;
        $animal = $case->animal;
        $isClosed = in_array($case->status, HospitalCaseStatus::completed(), true);
        $canRegister = in_array($case->status->value, HospitalCaseStatus::visibleToDoctorValues(), true);

        return [
            'id' => 'hospital-'.$case->case_number,
            'case_number' => $case->case_number,
            'case_type' => 'hospital',
            'status' => $isClosed ? 'closed' : 'active',
            'status_label' => $isClosed ? 'مغلقة' : 'نشطة',
            'hospital_status' => $case->status->value,
            'hospital_status_label' => $case->status->label(),
            'animal_id' => $animal?->code ?? '',
            'animal_type' => $animal?->species ?? '',
            'animal_group' => $case->group,
            'animal_gender' => $animal?->gender,
            'animal_age' => $animal?->formattedAge(),
            'open_reason' => $case->chief_complaint,
            'initial_note' => $case->healthCase?->description,
            'opened_at' => $case->admitted_at?->toIso8601String(),
            'updated_at' => ($case->closed_at ?? $case->updated_at)?->toIso8601String(),
            'source_label' => 'إحالة علاج معتمدة',
            'can_register_procedure' => $canRegister,
            'can_close' => false,
            'procedures' => $this->mapProcedures($case),
            'nutrition_recommendations' => $this->mapNutritionRecommendations($case),
        ];
    }

    /** @return list<array<string, mixed>> */
    private function mapProcedures(FieldCase|HospitalCase $case): array
    {
        if (! $case->relationLoaded('procedures')) {
            $case->load(['procedures.nutritionRecommendation', 'procedures.recorder']);
        }

        return $case->procedures
            ->sortByDesc('recorded_at')
            ->values()
            ->map(fn (MedicalCaseProcedure $procedure) => array_filter([
                'id' => (string) $procedure->id,
                'diagnosis' => $procedure->diagnosis,
                'treatment' => $procedure->treatment,
                'note' => $procedure->note,
                'case_result' => $this->caseType === 'hospital' ? $procedure->case_result->value : null,
                'case_result_label' => $this->caseType === 'hospital' ? $procedure->case_result->label() : null,
                'recorded_at' => $procedure->recorded_at?->toIso8601String(),
                'recorder_name' => $procedure->recorder?->name,
                'nutrition' => $procedure->nutritionRecommendation ? [
                    'recommendation_text' => $procedure->nutritionRecommendation->recommendation_text,
                    'start_date' => $procedure->nutritionRecommendation->start_date?->format('Y-m-d'),
                    'end_date' => $procedure->nutritionRecommendation->end_date?->format('Y-m-d'),
                    'note' => $procedure->nutritionRecommendation->note,
                ] : null,
            ], fn ($value) => $value !== null))
            ->all();
    }

    /** @return list<array<string, mixed>> */
    private function mapNutritionRecommendations(FieldCase|HospitalCase $case): array
    {
        return collect($this->mapProcedures($case))
            ->pluck('nutrition')
            ->filter()
            ->values()
            ->all();
    }

    public static function fromFieldCase(FieldCase $case): self
    {
        return new self($case, 'field');
    }

    public static function fromHospitalCase(HospitalCase $case): self
    {
        return new self($case, 'hospital');
    }
}
