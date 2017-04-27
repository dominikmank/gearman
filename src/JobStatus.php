<?php
namespace dmank\gearman;

class JobStatus
{
    /**
     * @var bool
     */
    private $isKnown;
    /**
     * @var bool
     */
    private $isRunning;

    public function __construct(array $status = array())
    {
        $this->isKnown = array_key_exists(0, $status) ? $status[0] : false;
        $this->isRunning = array_key_exists(1, $status) ? $status[1] : false;
    }

    /**
     * @return bool
     */
    public function isKnown()
    {
        return (bool)$this->isKnown;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return (bool)$this->isRunning;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return (!$this->isKnown() && !$this->isRunning());
    }
}
