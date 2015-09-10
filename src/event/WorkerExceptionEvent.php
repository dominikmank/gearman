<?php
namespace dmank\gearman\event;

use dmank\gearman\Worker;
use Symfony\Component\EventDispatcher\Event;

class WorkerExceptionEvent extends Event
{

    const EVENT_ON_FAILURE = 'worker.on_failure';

    /**
     * @var Worker
     */
    private $worker;

    /**
     * @var \Exception
     */
    private $exception;

    public function __construct(Worker $worker, \Exception $exception)
    {
        $this->worker = $worker;
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return Worker
     */
    public function getWorker()
    {
        return $this->worker;
    }
}
