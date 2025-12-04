<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    // Icon identifier
    'whereisit' => [
        // Icon provider class
        'provider' => SvgIconProvider::class,
        // The source SVG for the SvgIconProvider
        'source' => 'EXT:whereisit/Resources/Public/Icons/Extension.svg',
    ],
];
