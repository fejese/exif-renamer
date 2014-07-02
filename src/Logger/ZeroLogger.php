<?php

namespace fejese\ExifRenamer\Logger;

class ZeroLogger implements LoggerInterface
{
    /**
     * @see LoggerInterface::log()
     */
    public function log($message)
    {
    }

    /**
     * @see LoggerInterface::logLine()
     */
    public function logLine($line)
    {
    }

    public function __destruct()
    {
    }
}

