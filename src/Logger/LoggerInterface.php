<?php

namespace fejese\ExifRenamer\Logger;

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

