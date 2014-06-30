<?php

namespace fejese\ExifRenamer;

use fejese\ExifRenamer\Logger\LoggerInterface;
use fejese\ExifRenamer\Logger\DefaultLogger;

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

    /**
     * Returns the final new name for the original base name
     *
     * @param string $baseName
     * @param string $ext
     * @return string
     */
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

    /**
     * Runs the renaming process
     */
    public function rename()
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

