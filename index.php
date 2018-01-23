<?php

set_time_limit(0);
ini_set('memory_limit', '-1');

class RecursiveDirectoryIteration {

    public function readFiles($path, $filesOnly, $relativePathExcludes = array()) {

        if (!$path) {
            throw new Exception('Path could not be emapty');
        }

        $realpath = realpath($path);
        if (!$realpath) {
            throw new Exception('Path not found: ' . $path);
        }

        $files = array();
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($realpath), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($objects as $name => $object) {
            if (!($filesOnly && $object->isDir())) {

                $relativePath = str_replace($realpath, '', $object->getRealPath());
                if ($relativePath[0] == DIRECTORY_SEPARATOR) {
                    $relativePath = substr($relativePath, 1);
                }

                $excluded = false;
                foreach ($relativePathExcludes as $relativePathExclude) {
                    if (strstr($relativePath, $relativePathExclude) !== false) {
                        $excluded = true;
                    }
                }

                if (!$excluded) {
                    $files[] = array(
                        'isDir' => $object->isDir(),
                        'size' => $object->getSize(),
                        'filename' => $object->getFilename(),
                        'basename' => $object->getBasename(),
                        'extension' => $object->getExtension(),
                        'path' => $object->getPath(),
                        'realPath' => $object->getRealPath(),
                        'relativePath' => $relativePath,
                    );
                }
            }
        }

        return $files;
    }

}

class DiffReport {
    
    private $tooMany;
    
    private $deletedFiles;
    
    private $modifiedFiles;
    
    private $extraFiles;
    
    public function __construct($tooMany, $deletedFiles, $modifiedFiles, $extraFiles) {
        $this->tooMany = $tooMany;
        $this->deletedFiles = $deletedFiles;
        $this->modifiedFiles = $modifiedFiles;
        $this->extraFiles = $extraFiles;
    }
    
    public function getTooMany() {
        return $this->tooMany;
    }
    
    public function getDeletedFiles() {
        return $this->deletedFiles;
    }
    
    public function getModifiedFiles() {
        return $this->modifiedFiles;
    }
    
    public function getExtraFiles() {
        return $this->extraFiles;
    }
    
}

class DiffReportConfig {
    
    private $testPath;
    
    private $controlPath;
    
    private $excludedPaths;
    
    private $dieAfter;
    
    public function __construct($testPath, $controlPath, $excludedPaths, $dieAfter) {
        $this->testPath = $testPath;
        $this->controlPath = $controlPath;
        $this->excludedPaths = $excludedPaths;
        $this->dieAfter = $dieAfter;
    }
    
    public function getTestPath() {
        return $this->testPath;
    }
    
    public function getControlPath() {
        return $this->controlPath;
    }
    
    public function getExcludedPaths() {
        return $this->excludedPaths;
    }
    
    public function getDieAfter() {
        return $this->dieAfter;
    }
    
}

class DiffReporter {
    
    private $testPath;
    
    private $controlPath;
    
    private $excludedPaths;
    
    private $dieAfter;
    
    private $testFiles;
    
    private $controlFiles;
    
    public function __construct(DiffReportConfig $config) {
        $this->testPath = $config->getTestPath();
        $this->controlPath = $config->getControlPath();
        $this->excludedPaths = $config->getExcludedPaths();
        $this->dieAfter = $config->getDieAfter();
        $this->testFiles = null;
        $this->controlFiles = null;
    }
    
    private function readTestAndControlFiles() {
        $recursiveDirectoryIteration = new RecursiveDirectoryIteration();
        $this->testFiles = $recursiveDirectoryIteration->readFiles($this->testPath, true, $this->excludedPaths);
        $this->controlFiles = $recursiveDirectoryIteration->readFiles($this->controlPath, true, $this->excludedPaths);
        unset($recursiveDirectoryIteration);
    }
    
    public function getTestFiles() {
        if(is_null($this->testFiles)) {
            $this->readTestAndControlFiles();
        }
        return $this->testFiles;
    }
    
    public function getControlFiles() {
        if(is_null($this->controlFiles)) {
            $this->readTestAndControlFiles();
        }
        return $this->controlFiles;
    }
    
    public function getDieAfter() {
        return $this->dieAfter;
    }
    
    public function getReport() {
        
        
        $testFiles = $this->getTestFiles();
        $controlFiles = $this->getControlFiles();
        $dieAfter = $this->getDieAfter();
        

        $tooMany = false;

        $i = 0;

        $deletedFiles = array();
        $modifiedFiles = array();
        foreach ($testFiles as $testFile) {

            $found = false;
            $differents = false;
            foreach ($controlFiles as $controlFile) {
                if ($testFile['relativePath'] === $controlFile['relativePath']) {
                    $found = true;
                    if ($testFile['size'] != $controlFile['size']) {
                        $differents = true;
                    } else {
                        $testContents = file_get_contents($testFile['realPath']);
                        $controlContents = file_get_contents($controlFile['realPath']);
                        if ($testContents !== $controlContents) {
                            $differents = true;
                        }
                    }
                    break;
                }
            }

            if (!$found) {
                $deletedFiles[] = array(
                    'testFile' => $testFile,
                    'controlFile' => null,
                );
            } else {
                if ($differents) {
                    $modifiedFiles[] = array(
                        'testFile' => $testFile,
                        'controlFile' => $controlFile,
                    );
                }
            }

            $i++;
//    if($i % 1000 === 0) {
//        echo "$i...\n";
//    }
            if ($dieAfter && $i > $dieAfter) {
                $tooMany = true;
                break;
            }
        }

        $i = 0;
        $extraFiles = array();
        foreach ($controlFiles as $controlFile) {

            $found = false;
            foreach ($testFiles as $testFile) {
                if ($testFile['relativePath'] === $controlFile['relativePath']) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $extraFiles[] = array(
                    'testFile' => null,
                    'controlFile' => $controlFile,
                );
            }

            $i++;
//    if($i % 1000 === 0) {
//        echo "$i...\n";
//    }
            if ($dieAfter && $i > $dieAfter) {
                $tooMany = true;
                break;
            }
        }
        
        
        $report = new DiffReport($tooMany, $deletedFiles, $modifiedFiles, $extraFiles);
        
        return $report;
        
    }

    public function showReport(DiffReport $report) {

        $tooMany = $report->getTooMany();
        $deletedFiles = $report->getDeletedFiles();
        $modifiedFiles = $report->getModifiedFiles();
        $extraFiles = $report->getExtraFiles();


        echo "<pre>\n";

        if ($tooMany) {
            echo "WARNING: TOO MANY DIFFS!\n\n";
        }

        echo "Deleted files: (found in upgraded instance but not in repository)\n";
        foreach ($deletedFiles as $deletedFile) {
            if ($deletedFile['controlFile'] != null) {
                throw new Exception('incorrection 1');
            }
            echo "{$deletedFile['testFile']['relativePath']}\n";
        }
        echo "\n";

        echo "Modified files: (found in both but differents contents)\n";
        foreach ($modifiedFiles as $modifiedFile) {
            if ($modifiedFile['controlFile']['relativePath'] != $modifiedFile['testFile']['relativePath']) {
                throw new Exception('incorrection 2');
            }
            echo "{$modifiedFile['controlFile']['relativePath']}\n";
        }
        echo "\n";

        echo "Extra files: (found in repository but not in upgraded instance)\n";
        foreach ($extraFiles as $extraFile) {
            if ($extraFile['testFile'] != null) {
                throw new Exception('incorrection 3');
            }
            echo "{$extraFile['controlFile']['relativePath']}\n";
        }
        echo "\n";
    }

}

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
);

$diffConfig = new DiffReportConfig($testPath, $controlPath, $excludedPaths, $dieAfter);

$diffReporter = new DiffReporter($diffConfig);
$report = $diffReporter->getReport();
$diffReporter->showReport($report);

