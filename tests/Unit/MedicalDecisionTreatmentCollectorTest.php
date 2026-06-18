<?php

namespace Tests\Unit;

use App\Services\MedicalDecisionTreatmentCollector;
use PHPUnit\Framework\TestCase;

class MedicalDecisionTreatmentCollectorTest extends TestCase
{
    public function test_it_collects_unique_non_empty_treatments_from_follow_ups(): void
    {
        $collector = new MedicalDecisionTreatmentCollector();

        $treatments = $collector->fromFollowUps([
            ['treatment' => 'مضاد حيوي واسع الطيف'],
            ['treatment' => 'ضمادات يومية'],
            ['treatment' => 'مضاد حيوي واسع الطيف'],
            ['treatment' => ''],
            ['treatment' => '   '],
        ]);

        $this->assertSame([
            'مضاد حيوي واسع الطيف',
            'ضمادات يومية',
        ], $treatments);
    }
}
