<?php

namespace fejese\ExifRenamer\Tests\DataProvider;

class Format
{
    public static function getFormats()
    {
        $formats = array(
            array('Y-m-d_H-i-s'),
            array('ymdhis'),
            array('U'),
            array('y m d - g A')
        );

        return $formats;
    }
}

