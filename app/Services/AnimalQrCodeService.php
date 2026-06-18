<?php

namespace App\Services;

use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class AnimalQrCodeService
{
    public function svg(string $data): string
    {
        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64' => false,
            'scale' => 8,
            'addQuietzone' => true,
            'cssClass' => 'animal-qr-svg',
        ]);

        return (new QRCode($options))->render($data);
    }
}
