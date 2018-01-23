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
