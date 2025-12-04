<?php
if (!defined('TYPO3')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Whereisit',
    'List',
    'Inhaltselemente: Suche und Liste',
    'whereisit',
    'plugins',
    'Search where the content elements are placed'
);
