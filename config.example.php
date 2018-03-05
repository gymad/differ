<?php

$dieAfter = 0;

//$testPath = '/var/www/html/SuiteCRM_test';              // pointing to an upgraded/installed instance
//$testPath = '/var/www/html/SuiteCRM-7.9.14';
//$testPath = '/var/www/html/SuiteAssured';
//$testPath = '/var/www/html/SuiteCRM-7.10.1';
$testPath = '/var/www/html/SuiteCRM';

//$controlPath = '/var/www/html/SuiteCRM';
//$controlPath = '/var/www/html/SuiteCRM_controltest';    // pointing to a clean repository instance
$controlPath = '/var/www/html/SuiteCRM_test';
//$controlPath = '/var/www/html/SA_upgrade_test';
//$controlPath = '/var/www/html/SuiteCRM-7.10.1';

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

$excludedFiles = array(
    'suitecrm_version.php',
    'codeception.yml',
    '.pullapprove.yml',
    '.travis.yml',
    'status.json',
    'config.php',
    'composer.lock',
    'config_override.php',
    'install.log',
    '.htaccess',
    'sugarcrm.log',
    'suitecrm.log',
    'upgradeWizard.log',
);
