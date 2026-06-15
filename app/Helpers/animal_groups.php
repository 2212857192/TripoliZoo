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
            'القططية' => 'ANM',
            'الطيور' => 'BRD',
            'الزواحف' => 'RPT',
            'القرود' => 'MON',
            'الغزلان' => 'GZL',
            'الثدييات الكبيرة' => 'LRG',
            'الثدييات الصغيرة' => 'SML',
            'الدب واللامة' => 'BLA',
        ];
    }
}
