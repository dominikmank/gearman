<?php
namespace dmank\gearman\event;

use dmank\gearman\Job;
use dmank\gearman\JobHandler;
use Symfony\Component\EventDispatcher\Event;

class FunctionEvent extends Event
{
    const FUNCTION_BEFORE_EXECUTE = 'worker.function.before_execute';
    const FUNCTION_AFTER_EXECUTE = 'worker.function.after_execute';

    private $jobHandler;
    private $job;

    public function __construct(JobHandler $jobHandler, Job $job)
    {
        $this->jobHandler = $jobHandler;
        $this->job = $job;
    }

    /**
     * @return JobHandler
     */
    public function getJobHandler()
    {
        return $this->jobHandler;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }
}