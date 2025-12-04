<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Whereisit',
    'List',
    [
        \Npostnik\Whereisit\Controller\ContentController::class => 'list',
    ],
    // non-cacheable actions
    [
        \Npostnik\Whereisit\Controller\ContentController::class => 'list',
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);
