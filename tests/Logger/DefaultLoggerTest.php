<?php

namespace fejese\ExifRenamer\Tests\Logger;

use fejese\ExifRenamer\Logger\DefaultLogger;

class DefaultLoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var resource
     */
    protected $tmpFile = null;

    /**
     * @var DefaultLogger
     */
    protected $logger;

    public function setUp()
    {
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'log');
        $this->logger = new DefaultLogger($this->tmpFile);
    }

    /**
     * @dataProvider messageProvider
     */
    public function testLogWritesToFileNameGiven(array $messages)
    {
        ob_start();
        foreach ($messages as $message) {
            $this->logger->log($message);
        }
        ob_end_clean();
        $this->assertEquals(
            implode('', $messages),
            file_get_contents($this->tmpFile)
        );
    }

    /**
     * @dataProvider messageProvider
     */
    public function testLogWritesToStandardOut(array $messages)
    {
        ob_start();
        foreach ($messages as $message) {
            $this->logger->log($message);
        }
        $output = ob_get_clean();
        $this->assertEquals(
            implode('', $messages),
            $output
        );
    }

    /**
     * @dataProvider messageProvider
     */
    public function testLogLineWritesToFileNameGiven(array $messages)
    {
        ob_start();
        foreach ($messages as $message) {
            $this->logger->logLine($message);
        }
        ob_end_clean();
        $this->assertEquals(
            implode("\n", $messages) . "\n",
            file_get_contents($this->tmpFile)
        );
    }

    /**
     * @dataProvider messageProvider
     */
    public function testLogLineWritesToStandardOut(array $messages)
    {
        ob_start();
        foreach ($messages as $message) {
            $this->logger->logLine($message);
        }
        $output = ob_get_clean();
        $this->assertEquals(
            implode("\n", $messages) . "\n",
            $output
        );
    }

    public function tearDown()
    {
        if ($this->tmpFile) {
            unlink($this->tmpFile);
        }
    }

    public function messageProvider()
    {
        $messages = array(
            array(array('Test message')),
            array(array('Message with trailing space ')),
            array(array("Message with new line\n")),
            array(array("Multi \n line\nmessage")),
            array(
                array(
                    'Multi part ',
                    'message example',
                    "with new line\n",
                    'and trailing space '
                )
            )
        );

        return $messages;
    }
}


