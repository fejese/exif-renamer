<?php

namespace fejese\ExifRenamer\Tests\DataProvider;

class Message
{
    public static function getMessageLists()
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

