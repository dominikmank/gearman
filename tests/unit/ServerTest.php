<?php
namespace tests\dmank\gearman;

use dmank\gearman\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHost()
    {
        $server = new Server('foo');
        $this->assertEquals('foo', $server->getHost(), 'Host must be foo, since we give it in constructor!');
    }

    public function testGetPort()
    {
        $server = new Server('foo', 12);

        $this->assertEquals(12, $server->getPort(), 'Port must be 12, since we give it in constructor!');
    }
}
