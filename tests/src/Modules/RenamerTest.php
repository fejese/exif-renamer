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

    /**
     * @dataProvider fejese\ExifRenamer\Tests\DataProvider\Format::getFormats()
     */
    public function testCanSetFormat($format)
    {
        $this->renamer->setFormat($format);
        $this->assertEquals(
            $format,
            $this->renamer->getFormat()
        );
    }
}

