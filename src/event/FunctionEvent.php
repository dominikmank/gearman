<?php
namespace dmank\gearman\event;

use dmank\gearman\JobHandlerInterface;
use dmank\gearman\JobInterface;
use Symfony\Component\EventDispatcher\Event;

class FunctionEvent extends Event
{
    const FUNCTION_BEFORE_EXECUTE = 'worker.function.before_execute';
    const FUNCTION_AFTER_EXECUTE = 'worker.function.after_execute';

    private $jobHandler;
    private $job;
    /**
     * @var array
     */
    private $result;

    public function __construct(JobHandlerInterface $jobHandler, JobInterface $job, $result = [])
    {
        $this->jobHandler = $jobHandler;
        $this->job = $job;
        $this->result = $result;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return JobHandlerInterface
     */
    public function getJobHandler()
    {
        return $this->jobHandler;
    }

    /**
     * @return JobInterface
     */
    public function getJob()
    {
        return $this->job;
    }
}
