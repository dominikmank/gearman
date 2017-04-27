<?php
namespace dmank\gearman\event;

use dmank\gearman\Job;
use dmank\gearman\JobHandlerInterface;
use Symfony\Component\EventDispatcher\Event;

class FunctionFailureEvent extends Event
{
    const FUNCTION_ON_FAILURE  = 'worker.function.on_failure';

    /**
     * @var JobHandlerInterface
     */
    private $jobHandler;

    /**
     * @var Job|null
     */
    private $job;

    /**
     * @var \Exception
     */
    private $exception;

    public function __construct(JobHandlerInterface $jobHandler, \Exception $exception, Job $job = null)
    {
        $this->jobHandler = $jobHandler;
        $this->exception = $exception;
        $this->job = $job;
    }

    /**
     * @return JobHandlerInterface
     */
    public function getJobHandler()
    {
        return $this->jobHandler;
    }

    /**
     * @return Job|null
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
