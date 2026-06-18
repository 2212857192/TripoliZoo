<?php

if (! function_exists('animal_groups')) {
    /**
     * المجموعات الرسمية بالترتيب المعتمد.
     *
     * @return list<string>
     */
    function animal_groups(): array
    {
        return [
            'القططية',
            'الطيور',
            'الزواحف',
            'القرود',
            'الغزلان',
            'الثدييات الكبيرة',
            'الثدييات الصغيرة',
            'الدب واللامة',
        ];
    }
}

if (! function_exists('animal_group_prefixes')) {
    /**
     * بادئات أرقام الحيوانات لكل مجموعة.
     *
     * @return array<string, string>
     */
    function animal_group_prefixes(): array
    {
        return [
            'القططية' => 'C',
            'الطيور' => 'B',
            'الزواحف' => 'R',
            'القرود' => 'M',
            'الغزلان' => 'G',
            'الثدييات الكبيرة' => 'L',
            'الثدييات الصغيرة' => 'S',
            'الدب واللامة' => 'D',
        ];
    }
}

if (! function_exists('quarantine_code_prefix')) {
    function quarantine_code_prefix(): string
    {
        return 'Q';
    }
}
