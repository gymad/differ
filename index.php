<?php

set_time_limit(0);
ini_set('memory_limit', '-1');

include_once __DIR__ . '/DiffReport.php';
include_once __DIR__ . '/DiffReportConfig.php';
include_once __DIR__ . '/DiffReporter.php';
include_once __DIR__ . '/RecursiveDirectoryIteration';


// ---------------- START ENTRY HERE -------------------

$dieAfter = 0;

$testPath = '/var/www/html/SuiteCRM_test';              // pointing to an upgraded instance
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

$diffConfig = new DiffReportConfig($testPath, $controlPath, $excludedPaths, $dieAfter);

$diffReporter = new DiffReporter($diffConfig);
$report = $diffReporter->getReport();
$diffReporter->showReport($report);

