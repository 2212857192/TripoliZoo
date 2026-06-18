<?php

namespace Tests\Unit;

use App\Models\Animal;
use App\Services\AnimalCodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimalCodeGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_quarantine_codes_sequentially(): void
    {
        $generator = app(AnimalCodeGenerator::class);

        $this->assertSame('Q001', $generator->nextForQuarantine());

        Animal::create([
            'code' => 'Q001',
            'species' => 'قرد',
            'group' => 'القرود',
            'gender' => 'ذكر',
            'status' => 'quarantine',
        ]);

        $this->assertSame('Q002', $generator->nextForQuarantine());
    }

    public function test_generates_group_codes_by_prefix(): void
    {
        $generator = app(AnimalCodeGenerator::class);

        $this->assertSame('C001', $generator->nextForGroup('القططية'));
        $this->assertSame('M001', $generator->nextForGroup('القرود'));
        $this->assertSame('B001', $generator->nextForGroup('الطيور'));
        $this->assertSame('R001', $generator->nextForGroup('الزواحف'));
        $this->assertSame('G001', $generator->nextForGroup('الغزلان'));
        $this->assertSame('S001', $generator->nextForGroup('الثدييات الصغيرة'));
        $this->assertSame('L001', $generator->nextForGroup('الثدييات الكبيرة'));
        $this->assertSame('D001', $generator->nextForGroup('الدب واللامة'));

        Animal::create([
            'code' => 'C001',
            'species' => 'أسد',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'status' => 'active',
        ]);

        $this->assertSame('C002', $generator->nextForGroup('القططية'));
    }

    public function test_ignores_legacy_codes_with_different_format(): void
    {
        Animal::create([
            'code' => 'ANM-0012',
            'species' => 'أسد',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'status' => 'active',
        ]);

        $generator = app(AnimalCodeGenerator::class);

        $this->assertSame('C001', $generator->nextForGroup('القططية'));
    }
}
