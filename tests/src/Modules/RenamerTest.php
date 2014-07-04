<?php

namespace fejese\ExifRenamer\Tests\Modules;

use fejese\ExifRenamer\Renamer;

class RenamerTest extends \PHPUnit_Framework_TestCase
{
    private $renamer;

    public function setUp()
    {
        $this->renamer = new Renamer('.');
    }

    public function testCanSetFormat()
    {
        $format = 'ymdhis';
        $this->renamer->setFormat($format);
        $this->assertEquals(
            $format,
            $this->renamer->getFormat()
        );
    }
}

