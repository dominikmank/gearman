<?php
namespace dmank\gearman;

class Server
{
    /**
     * @var string
     */
    private $host = '';

    /**
     * @var int
     */
    private $port;

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($host = '127.0.0.1', $port = 4730)
    {
        $this->host = $host;
        $this->port = (int) $port;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }
}
