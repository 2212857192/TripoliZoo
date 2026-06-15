<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Platform display name (emails, visitor-facing copy)
    |--------------------------------------------------------------------------
    */

    'platform_name' => env('PLATFORM_NAME', 'Tripoli Zoo'),

    'platform_name_ar' => env('PLATFORM_NAME_AR', 'منصة حديقة حيوان طرابلس'),

    'password_reset_code_ttl' => (int) env('PASSWORD_RESET_CODE_TTL', 15),

];
