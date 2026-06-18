<?php

namespace App\Services;

use App\Models\Animal;
use InvalidArgumentException;

class AnimalCodeGenerator
{
    public function nextForQuarantine(): string
    {
        return $this->nextForPrefix(quarantine_code_prefix());
    }

    public function nextForGroup(string $group): string
    {
        $prefix = animal_group_prefixes()[$group] ?? null;

        if ($prefix === null) {
            throw new InvalidArgumentException("Unknown animal group: {$group}");
        }

        return $this->nextForPrefix($prefix);
    }

    /**
     * @return array<string, string>
     */
    public function peekNextByGroup(): array
    {
        $codes = [];

        foreach (animal_groups() as $group) {
            $codes[$group] = $this->nextForGroup($group);
        }

        return $codes;
    }

    private function nextForPrefix(string $prefix): string
    {
        $maxSequence = 0;
        $pattern = '/^'.preg_quote($prefix, '/').'(\d+)$/';

        Animal::query()
            ->withQuarantine()
            ->where('code', 'like', $prefix.'%')
            ->pluck('code')
            ->each(function (string $code) use ($pattern, &$maxSequence) {
                if (preg_match($pattern, $code, $matches)) {
                    $maxSequence = max($maxSequence, (int) $matches[1]);
                }
            });

        return $prefix.str_pad((string) ($maxSequence + 1), 3, '0', STR_PAD_LEFT);
    }
}
