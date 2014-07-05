<?php

namespace fejese\ExifRenamer\Tests\Modules;

use fejese\ExifRenamer\Renamer;
use fejese\ExifRenamer\Tests\Helpers\TestFileManager;

class RenamerTest extends \PHPUnit_Framework_TestCase
{
    private $renamer;

    private $testFilesSrc;
    private $testFilesTmp;

    public function setUp()
    {
        $this->testFileSrc =  __DIR__ . '/../../files';
        $this->testFilesTmp =  __DIR__ . '/../../tmp';
        $this->testFileManager = new TestFileManager(
            $this->testFileSrc,
            $this->testFilesTmp
        );
        $this->testFileManager->copyToTemp();

        $this->renamer = new Renamer($this->testFilesTmp);
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

    /**
     */
    public function testImagesWithoutExifRenamedToMTime()
    {
        $file = $this->testFilesTmp . '/noexif.png';
        $mtime = filemtime($file);
        $md5 = md5_file($file);

        $newFile = sprintf(
            '%s/%d_M.png',
            $this->testFilesTmp,
            $mtime
        );

        $this->renamer->setFormat('U');
        $this->renamer->rename();

        $this->assertTrue(file_exists($newFile));
        $this->assertEquals($md5, md5_file($newFile));
    }

    public function tearDown()
    {
        $this->testFileManager->cleanupTemp();
    }
}

