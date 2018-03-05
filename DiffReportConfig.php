<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DiffReportConfig
 *
 * @author gyula
 */

class DiffReportConfig {
    
    private $testPath;
    
    private $controlPath;
    
    private $excludedPaths;
    
    private $excludedFiles;
    
    private $dieAfter;
    
    public function __construct($testPath, $controlPath, $excludedPaths, $excludedFiles, $dieAfter) {
        $this->testPath = $testPath;
        $this->controlPath = $controlPath;
        $this->excludedPaths = $excludedPaths;
        $this->excludedFiles = $excludedFiles;
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
    
    public function getExcludedFiles() {
        return $this->excludedFiles;
    }
    
    public function getDieAfter() {
        return $this->dieAfter;
    }
    
}
