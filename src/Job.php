<?php
namespace dmank\gearman;

class Job
{
    private $workLoad;

    /**
     * @var string
     */
    private $jobName;

    public function __construct($jobName, $workLoad)
    {
        $this->jobName = (string)$jobName;
        $this->workLoad = $workLoad;
    }

    /**
     * @return string
     */
    public function getJobName()
    {
        return $this->jobName;
    }
    /**
     * @return mixed
     */
    public function getWorkLoad()
    {
        $workload = $this->workLoad;

        if (StringHelper::isSerialized($this->workLoad)) {
            $workload = unserialize($this->workLoad);
        }

        return $workload;
    }
}
