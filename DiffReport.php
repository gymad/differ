<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DiffReport
 *
 * @author gyula
 */

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
