<?php

namespace fejese\ExifRenamer\Tests\Modules\Logger;

use fejese\ExifRenamer\Logger\ZeroLogger;

class ZeroLoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var resource
     */
    protected $tmpFile = null;

    /**
     * @var ZeroLogger
     */
    protected $logger;

    public function setUp()
    {
        $this->logger = new ZeroLogger();
    }

    /**
     * @dataProvider fejese\ExifRenamer\Tests\DataProvider\Message::getMessageLists
     */
    public function testLogDoesNotGenerateOutput(array $messages)
    {
        ob_start();
        foreach ($messages as $message) {
            $this->logger->log($message);
        }
        $output = ob_get_clean();
        $this->assertEquals(0, strlen($output));
    }

    /**
     * @dataProvider fejese\ExifRenamer\Tests\DataProvider\Message::getMessageLists
     */
    public function testLogLineDoesNotGenerateOutput(array $messages)
    {
        ob_start();
        foreach ($messages as $message) {
            $this->logger->logLine($message);
        }
        $output = ob_get_clean();
        $this->assertEquals(0, strlen($output));
    }
}


