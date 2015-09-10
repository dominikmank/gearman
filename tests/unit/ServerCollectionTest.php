<?php
namespace tests\dmank\gearman;

use dmank\gearman\Server;
use dmank\gearman\ServerCollection;

class ServerCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddServer()
    {
        $serverCollection = new ServerCollection();
        $server = new Server();

        $this->assertCount(0, $serverCollection->getServers());

        $serverCollection->add($server);
        $this->assertCount(1, $serverCollection->getServers());

        foreach ($serverCollection->getServers() as $serverInCollection) {
            $this->assertSame($server, $serverInCollection);
        }
    }

    public function testRightCount()
    {
        $serverCollection = new ServerCollection();
        $this->assertEquals(0, $serverCollection->count());

        $serverCollection->add(new Server());
        $this->assertEquals(1, $serverCollection->count());
    }
}
