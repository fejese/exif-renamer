#!/usr/bin/env php
<?php

namespace fejese\ExifRename;

class LoggerException extends \Exception
{
}

interface LoggerInterface
{
    /**
     * Logs a message
     *
     * @param string $message
     * @throws LoggerException
     * @return self
     */
    public function log($message);

    /**
     * Logs a line of message and appends a new line
     *
     * @param string $line
     * @throws LoggerException
     * @return self
     */
    public function logLine($line);
}

class DefaultLogger implements LoggerInterface
{
    /**
     * @var string
     */
    private $logfileName;

    /**
     * @var resource
     */
    private $fileHandler = null;

    /**
     * Contructor setting the file name
     *
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->logfileName = $fileName;
    }

    /**
     * Retrieves the file handler
     *
     * @throws \Exception if file is not writable
     * @return resource
     */
    private function getFileHandler()
    {
        if (is_null($this->fileHandler)) {
            $handler = fopen($this->logfileName, 'w');
            if (!$handler) {
                throw new LoggerException(sprintf('Failed to open log file for writing', $this->logfileName));
            }
            $this->fileHandler = $handler;
        }

        return $this->fileHandler;
    }

    /**
     * @see LoggerInterface::log()
     */
    public function log($message)
    {
        $fileHandler = null;
        try {
            $fileHandler = $this->getFileHandler();
        } catch (LoggerException $le) {
            throw $le; // Bubble up
        }

        fwrite($fileHandler, $message);
        echo $message;

        return $this;
    }

    /**
     * @see LoggerInterface::logLine()
     */
    public function logLine($line)
    {
        try {
            return $this->log($line . "\n");
        } catch (LoggerException $le) {
            throw $le; // Bubble up
        }
    }

    public function __destruct()
    {
        if (!is_null($this->fileHandler)) {
            fclose($this->fileHandler);
            $this->fileHandler = null;
        }
    }
}

class Renamer
{
    /**
     * @var string[]
     */
    private $extensions = array(
        'jpg',
        'jpeg',
        'tif',
        'tiff',
        'avi'
    );

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $path;

    private function getNewBaseName ($entry)
    {
        $exifdata = @exif_read_data($this->path . '/' . $entry);
        if (!empty($exifdata['DateTimeOriginal'])) {
            $date = preg_replace(
                '/(20\d\d)\D*(\d\d)\D*(\d\d)\D*(\d\d)\D*(\d\d)\D*(\d\d)/',
                '$1$2$3_$4$5$6',
                $exifdata['DateTimeOriginal']
            );
            $newBaseName = $date;
        } else {
            $date = date('Ymd_His', filemtime($this->path . '/' . $entry));
            $newBaseName = $date . "_M";
        }

        return $newBaseName;
    }

    private function getNewFinalName($baseName, $ext)
    {
        $i=0;
        $newFinalName = $baseName . '.' . $ext;
        while (is_file($this->path . '/' . $newFinalName)) {
            $newFinalName = $baseName . "_$i." . $ext;
            $i++;
        }

        return $newFinalName;
    }

    public function rename ()
    {
        $filesToRename = scandir($this->path);
        foreach ($filesToRename as $oldName) {
            $this->getLogger()->log($oldName . ' ');

            $extension = strtolower(pathinfo($oldName, PATHINFO_EXTENSION));

            if (!in_array($extension, $this->extensions)) {
                $this->getLogger()->logLine("SKIPPED");
                continue;
            }

            $newBaseName = $this->getNewBaseName($oldName);
            if ($newBaseName . '.' . $extension == $oldName) {
                $this->getLogger()->logLine("LEAVE");
                continue;
            }

            $newFinalName = $this->getNewFinalName($newBaseName, $extension);
            if (!rename($this->path . '/' . $oldName, $this->path . '/' . $newFinalName)) {
                $this->getLogger()->logLine("ERROR: $newFinalName");
                continue;
            }

            $this->getLogger()->logLine($newFinalName);
        }
    }

    /**
     * Constructor setting the working path
     *
     * @param string $path
     */
    public function __construct($path = '.')
    {
        $this->path = $path;
    }

    /**
     * Sets the logger instance
     *
     * @param LoggerInterface $logger
     * @return self
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Returns the logger instance and initialises it if it is not set
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (is_null($this->logger)) {
            $logfile = sprintf(
                '%s/rename_%s.log',
                $this->path,
                date('ymdHis')
            );
            $this->logger = new DefaultLogger($logfile);
        }

        return $this->logger;
    }
}

if (isset($argv) && is_array($argv) && !empty($argv[0])) {
    if (realpath(__FILE__) === realpath($argv[0])) {
        date_default_timezone_set('Europe/London');
        $path = empty($argv[1]) ? '.' : $argv[1];
        $r = new Renamer($path);
        $r->rename();
    }
}

