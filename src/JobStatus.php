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
    /**
     * @var int
     */
    private $numerator;
    /**
     * @var int
     */
    private $denominator;

    public function __construct(array $status = array())
    {
        $this->isKnown = array_key_exists(0, $status) ? $status[0] : false;
        $this->isRunning = array_key_exists(1, $status) ? $status[1] : false;
        $this->numerator = array_key_exists(2, $status) ? $status[2] : 0;
        $this->denominator = array_key_exists(3, $status) ? $status[3] : 0;
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
