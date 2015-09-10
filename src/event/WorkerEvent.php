<?php
namespace dmank\gearman\event;

use dmank\gearman\Worker;
use Symfony\Component\EventDispatcher\Event;

class WorkerEvent extends Event
{
    const EVENT_BEFORE_RUN = 'worker.before_run';
    const EVENT_AFTER_RUN = 'worker.after_run';
    const EVENT_ON_IO_WAIT = 'worker.on_io_wait';
    const EVENT_ON_NO_JOBS = 'worker.on_no_jobs';
    const EVENT_ON_WORK = 'worker.on_work';
    const EVENT_ON_BEFORE_DESTROY = 'worker.on_before_destroy';

    /**
     * @var Worker
     */
    private $worker;

    public function __construct(Worker $workerInstance)
    {
        $this->worker = $workerInstance;
    }

    /**
     * @return Worker
     */
    public function getWorkerInstance()
    {
        return $this->worker;
    }
}
