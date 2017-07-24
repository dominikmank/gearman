<?php
namespace tests\dmank\gearman;

use dmank\gearman\StringHelper;

class StringHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider serializeProvider
     * @param $input
     * @param $expected
     */
    public function testIsSerialized($input, $expected)
    {
        $helperResult = StringHelper::isSerialized($input);
        $this->assertEquals($expected, $helperResult);
    }

    public function serializeProvider()
    {
        return array(
            'is serialized' => array(serialize('foo'), true),
            'is bool serialized' => array(serialize(false), true),
            'is not serialized' => array('foo', false)
        );
    }
}
