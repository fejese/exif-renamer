<?php

namespace fejese\ExifRenamer\Logger;

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

