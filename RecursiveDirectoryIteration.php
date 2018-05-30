<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RecursiveDirectoryIteration
 *
 * @author gyula
 */

class RecursiveDirectoryIteration {

    public function readFiles($path, $filesOnly, $relativePathExcludes = array(), $relativeFileExcludes = array()) {

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
                

                $relativeFile = $object->getFileName();
                if ($relativeFile[0] == DIRECTORY_SEPARATOR) {
                    $relativeFile = substr($relativeFile, 1);
                }
                
                
                foreach ($relativeFileExcludes as $relativeFileExclude) {
                    if (strstr($relativeFile, $relativeFileExclude) !== false) {
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

