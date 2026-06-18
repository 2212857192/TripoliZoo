<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $response = PlutuLaravel\Facades\PlutuAdfali::verify('0912345678', 10.0);
    $original = $response->getOriginalResponse();

    echo 'successful='.($original->isSuccessful() ? 'yes' : 'no').PHP_EOL;
    echo 'error='.$original->getErrorMessage().PHP_EOL;
    echo 'code='.$original->getErrorCode().PHP_EOL;
    echo 'status='.$original->getStatusCode().PHP_EOL;
    echo 'body='.json_encode($original->getBody()).PHP_EOL;
} catch (Throwable $e) {
    echo 'exception='.$e->getMessage().PHP_EOL;
}
