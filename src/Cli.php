<?php

namespace fejese\ExifRenamer;

class Cli
{
    const EXIT_CODE_NORMAL = 0;
    const EXIT_CODE_INV_PARAM = 1;
    const EXIT_CODE_INV_FORMAT = 2;

    protected function printHelp()
    {
        echo <<<HELP
Exif renamer tool

Usage: exif-rename [-p|--path <path> [-p|--path <path> ...]] [-f|--format <format>]

Multiple path option is allowed but only one format. If no path is sepcified current
folder will be processed.


HELP;
    }

    protected function printError($message)
    {
        $out = fopen('php://stderr', 'w');
        fwrite($out, sprintf('Error: %s%s%s', $message, PHP_EOL, PHP_EOL));
        fclose($out);
    }

    protected function getPaths($options)
    {
        $paths = array();
        foreach (array('p', 'path') as $param) {
            if (array_key_exists($param, $options)) {
                if (is_array($options[$param])) {
                    $paths = array_merge($paths, $options[$param]);
                } else {
                    $paths[] = $options[$param];
                }
            }
        }

        if (count($paths) == 0) {
            $paths[] = '.';
        }

        return $paths;
    }

    protected function getFormat($options)
    {
        $format = null;
        foreach (array('f', 'format') as $param) {
            if (array_key_exists($param, $options)) {
                if (is_array($options[$param]) || $format !== null) {
                    $this->printError('Only one format specification is allowed');
                    return false;
                } else {
                    $format = $options[$param];
                }
            }
        }

        return $format;
    }

    public function run()
    {
        $options = getopt(
            'p:f:h',
            array(
                'path:',
                'format:',
                'help'
            )
        );

        if (!$options) {
            $this->printError('invalid parameters');
            $this->printHelp();
            return self::EXIT_CODE_INV_PARAM;
        }

        if (array_key_exists('h', $options) || array_key_exists('help', $options)) {
            $this->printHelp();
            return self::EXIT_CODE_NORMAL;
        }

        $paths = $this->getPaths($options);

        $format = $this->getFormat($options);
        if ($format === false) {
            return self::EXIT_CODE_INV_FORMAT;
        }

        foreach ($paths as $path) {
            $renamer = new Renamer($path);
            if (!is_null($format)) {
                $renamer->setFormat($format);
            }
            $renamer->rename();
        }
    }
}

