<?php

namespace dmank\gearman\event;

use dmank\gearman\ServerCollection;
use Symfony\Component\EventDispatcher\Event;

class ConnectToServerEvent extends Event
{

    const CONNECT_TO_SERVER_EVENT = 'worker.server_on_connect';
    const CONNECTED_TO_SERVER_EVENT = 'worker.server_on_connected';

    /**
     * @var ServerCollection
     */
    private $serverCollection;

    public function __construct(ServerCollection $serverCollection)
    {
        $this->serverCollection = $serverCollection;
    }

    /**
     * @return ServerCollection
     */
    public function getServerCollection()
    {
        return $this->serverCollection;
    }
}