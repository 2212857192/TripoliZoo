<?php

namespace App\Services;

use App\Models\Quarantine;

class MedicalDecisionTreatmentCollector
{
    /**
     * @param  iterable<int, array{treatment?: string|null}|object>  $followUps
     * @return list<string>
     */
    public function fromFollowUps(iterable $followUps): array
    {
        $treatments = [];

        foreach ($followUps as $followUp) {
            $treatment = trim($this->treatmentFromFollowUp($followUp));

            if ($treatment === '') {
                continue;
            }

            $treatments[] = $treatment;
        }

        return array_values(array_unique($treatments));
    }

    public function fromQuarantine(Quarantine $quarantine): array
    {
        $quarantine->loadMissing('vaccines');

        $treatments = [];

        foreach ($quarantine->vaccines as $vaccine) {
            $name = trim((string) $vaccine->name);

            if ($name === '') {
                continue;
            }

            $treatments[] = $name;
        }

        return array_values(array_unique($treatments));
    }

    private function treatmentFromFollowUp(array|object $followUp): string
    {
        if (is_array($followUp)) {
            return (string) ($followUp['treatment'] ?? '');
        }

        return (string) ($followUp->treatment ?? '');
    }
}
