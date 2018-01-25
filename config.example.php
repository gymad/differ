<?php

$dieAfter = 0;

$testPath = '/var/www/html/SuiteCRM_test';              // pointing to an upgraded/installed instance
//$testPath = '/var/www/html/SuiteCRM-7.9.10';
$controlPath = '/var/www/html/SuiteCRM_controltest';    // pointing to a clean repository instance
$excludedPaths = array(
    'cache/',
    'tests/',
    'vendor/',
    'custom/',
    '.git',
    '.github/',
    '.sass-cache/',
    'upload/',
    'themes/',
);
