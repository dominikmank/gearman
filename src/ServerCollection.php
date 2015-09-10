<?php
namespace dmank\gearman;

class ServerCollection implements \Countable
{
    /**
     * @var array
     */
    private $servers;

    public function __construct()
    {
        $this->servers = array();
    }

    /**
     * @param Server $server
     */
    public function add(Server $server)
    {
        $this->servers[] = $server;
    }

    /**
     * @return array
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->servers);
    }
}
