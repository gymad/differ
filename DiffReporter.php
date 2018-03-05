<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DiffReporter
 *
 * @author gyula
 */

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

        echo "Deleted files: (found in upgraded/installed instance but not in repository)\n";
        if($deletedFiles) {
            foreach ($deletedFiles as $deletedFile) {
                if ($deletedFile['controlFile'] != null) {
                    throw new Exception('incorrection 1');
                }
                echo "{$deletedFile['testFile']['relativePath']}\n";
            }
        } else {
            echo "not found any...";
        }
        echo "\n";

        echo "Modified files: (found in both but differents contents)\n";
        if($modifiedFiles) {
            foreach ($modifiedFiles as $modifiedFile) {
                if ($modifiedFile['controlFile']['relativePath'] != $modifiedFile['testFile']['relativePath']) {
                    throw new Exception('incorrection 2');
                }
                echo "{$modifiedFile['controlFile']['relativePath']}\n";
            }
        } else {
            echo "not found any...";
        }
        echo "\n";

        echo "Extra files: (found in repository but not in upgraded/installed instance)\n";
        if($extraFiles) {
            foreach ($extraFiles as $extraFile) {
                if ($extraFile['testFile'] != null) {
                    throw new Exception('incorrection 3');
                }
                echo "{$extraFile['controlFile']['relativePath']}\n";
            }
        } else {
            echo "not found any...";
        }
        echo "\n";
    }

}
