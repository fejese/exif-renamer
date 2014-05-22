<?php

class ExifRename {
    private static $logfile = 'rename.log';

    private static $extensions = array (
        'jpg',
        'jpeg',
        'tif',
        'tiff',
        'avi'
    );

    private static $exceptions = array (
        '.',
        '..',
        '.svn',
        'rename'
    );

    private $loghandler;
    private $path;

    private function log($oldname = null, $info = null) {
        ob_start();
        if ($oldname != null) {
            echo "\n$oldname -> ";
        }
        if ($info != null) {
            echo "$info ";
        }
        $out = ob_get_clean();
        fwrite($this->loghandler, $out);
        echo $out;
    }

    private function getNewBaseName ($entry) {
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

    private function getNewFinalName($baseName, $ext) {
        $i=0;
        $newFinalName = $baseName . '.' . $ext;
        while (is_file($this->path . '/' . $newFinalName)) {
            $newFinalName = $baseName . "_$i." . $ext;
            $i++;
        }

        return $newFinalName;
    }

    public function rename () {
        $filesToRename = scandir($this->path);
        foreach ($filesToRename as $oldName) {
            $this->log($oldName);

            $extension = strtolower(pathinfo($oldName, PATHINFO_EXTENSION));

            try {
                if (!in_array($extension, self::$extensions) || in_array($oldName, self::$exceptions)) {
                    throw new Exception("SKIPPED");
                }

                $newBaseName = $this->getNewBaseName($oldName);
                if ($newBaseName . '.' . $extension == $oldName) {
                    throw new Exception("LEAVE");
                }

                $newFinalName = $this->getNewFinalName($newBaseName, $extension);
                if (!rename($this->path . '/' . $oldName, $this->path . '/' . $newFinalName)) {
                    throw new Exception("ERROR: $newFinalName");
                }

                $this->log($newFinalName);
            } catch (Exception $e) {
                $this->log(null, $e->getMessage());
            }
        }
    }

    public function __construct($path = '.') {
        date_default_timezone_set('Europe/London');
        $this->path = $path;
        $logfile = sprintf(
            '%s/%s_%s.log',
            $this->path,
            self::$logfile,
            date('ymdHis')
        );
        $this->loghandler = fopen($logfile, 'w');
    }
    public function __destruct() {
        if ($this->loghandler !== null) {
            fclose($this->loghandler);
        }
    }
}

$r = new ExifRename();
$r->rename();

?>
