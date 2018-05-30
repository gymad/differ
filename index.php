<?php

echo "TODO: RecursiveDirectoryIteration in packagist. use: composer require gymad/rditerator\n";

set_time_limit(0);
ini_set('memory_limit', '-1');

include_once __DIR__ . '/DiffReport.php';
include_once __DIR__ . '/DiffReportConfig.php';
include_once __DIR__ . '/DiffReporter.php';
include_once __DIR__ . '/RecursiveDirectoryIteration.php'; // TODO: use packagist version with composer


// ---------------- START ENTRY HERE -------------------

include __DIR__ . '/config.example.php';

$diffConfig = new DiffReportConfig($testPath, $controlPath, $excludedPaths, $excludedFiles, $dieAfter);

$diffReporter = new DiffReporter($diffConfig);
$report = $diffReporter->getReport();
$diffReporter->showReport($report);

