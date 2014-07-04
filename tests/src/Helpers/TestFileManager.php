<?php

namespace fejese\ExifRenamer\Tests\Helpers;

class TestFileManager
{
    private $fileDir;
    private $tempDir;

    public function __construct($fileDir, $tempDir)
    {
        $this->fileDir = $fileDir;
        $this->tempDir = $tempDir;
    }

    private function copyFolder($from, $to)
    {
        $ds = DIRECTORY_SEPARATOR;
        $from = rtrim($from, $ds);
        $to = rtrim($to, $ds);

        if (!is_dir($to)) {
            mkdir($to);
        }

        foreach (scandir($from) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if ($file === '.gitignore') {
                continue;
            }
            $fromFile = $from . $ds . $file;
            $toFile = $to . $ds . $file;
            if (is_dir($fromFile)) {
                $this->copyFolder($fromFile, $toFile);
            } else {
                copy($fromFile, $toFile);
            }
        }
    }

    private function cleanupFolder($folder)
    {
        foreach (scandir($folder) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if ($file === '.gitignore') {
                continue;
            }
            $path = $folder . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->cleanupFolder($path);
                rmdir($path);
            } else {
                unlink($path);
            }
        }
    }

    public function copyToTemp()
    {
        $this->copyFolder($this->fileDir, $this->tempDir);
    }

    public function cleanupTemp()
    {
        $this->cleanupFolder($this->tempDir);
    }
}
