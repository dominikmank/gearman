<?php

namespace dmank\gearman\event\subscriber;

use dmank\gearman\event\WorkerEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MaxRuntime implements EventSubscriberInterface
{
    /**
     * @var \DateTime
     */
    private $startTime;
    /**
     * @var string Datetime modify string
     */
    private $maxRunTime;

    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct($maxRuntime = '+2 hours', LoggerInterface $logger = null)
    {
        $this->startTime = new \DateTime();
        $this->maxRunTime = $maxRuntime;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            WorkerEvent::EVENT_BEFORE_RUN => array('onBeforeRun'),
            WorkerEvent::EVENT_AFTER_RUN => array('onAfterRun'),
            WorkerEvent::EVENT_ON_NO_JOBS => array('onNoJobs'),
        );
    }

    public function onBeforeRun(WorkerEvent $event)
    {
        if (!is_null($this->logger)) {
            $this->logger->notice(
                sprintf(
                    'If the max runtime of %s is exceeded, i will stop the worker.',
                    $this->maxRunTime
                )
            );
        }
    }

    public function onNoJobs(WorkerEvent $event)
    {
        if ($this->timeIsExceeded()) {
            $this->informLogger();

            $event->getWorkerInstance()->destroyWorker();
        }
    }

    public function onAfterRun(WorkerEvent $event)
    {
        if ($this->timeIsExceeded()) {
            $this->informLogger();

            $event->getWorkerInstance()->destroyWorker();
        }
    }

    private function timeIsExceeded()
    {
        /** @var \DateTime $afterTime */
        $afterTime = clone $this->startTime;
        $afterTime->modify($this->maxRunTime);

        $now = new \DateTime();

        return ($now > $afterTime);
    }

    private function informLogger()
    {
        if (!is_null($this->logger)) {
            $this->logger->notice(
                sprintf(
                    'Max Runtime exceeded. Started at %s, checked last at %s. Maxruntime is %s',
                    $this->startTime->format('d.m.Y H:i:s'),
                    date('d.m.Y H:i:s'),
                    $this->maxRunTime
                )
            );
        }
    }
}
