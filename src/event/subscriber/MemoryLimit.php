<?php

namespace dmank\gearman\event\subscriber;

use dmank\gearman\event\WorkerEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MemoryLimit implements EventSubscriberInterface
{
    private $maxMemoryLimit;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($maxMemoryLimit = -1, LoggerInterface $logger = null)
    {
        $this->maxMemoryLimit = $maxMemoryLimit;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            WorkerEvent::EVENT_AFTER_RUN => array('onAfterRun'),
            WorkerEvent::EVENT_BEFORE_RUN => array('onBeforeRun')
        );
    }

    public function onBeforeRun(WorkerEvent $event)
    {
        if (!is_null($this->logger) && $this->maxMemoryLimit != '-1') {
            $this->logger->notice(
                sprintf(
                    'i will stop the worker when the memory limit of %s is exceeded.',
                    $this->maxMemoryLimit
                )
            );
        }
    }

    public function onAfterRun(WorkerEvent $event)
    {
        if ($this->memoryLimitAlmostExceeded()) {
            $this->informLogger();

            $event->getWorkerInstance()->destroyWorker();
        }
    }

    /**
     * @return bool
     */
    private function memoryLimitAlmostExceeded()
    {
        return (
            $this->maxMemoryLimit != '-1' &&
            memory_get_usage(true) * 1.2 > $this->returnBytes($this->maxMemoryLimit)
        );
    }

    private function informLogger()
    {
        if (!is_null($this->logger)) {
            $this->logger->notice(
                sprintf(
                    'Memory limit almost exceeded. Max limit is %s, used %s so far.',
                    $this->maxMemoryLimit,
                    round((memory_get_usage(true) * 1.2) / 1048576, 2) . 'M'
                )
            );
        }
    }

    /**
     * @param $value
     * @return int
     */
    private function returnBytes($value)
    {
        $value = trim($value);
        $last = strtolower($value{strlen($value) - 1});

        switch ($last) {
            case 'g':
            case 'm':
            case 'k':
                $value *= 1024;
                break;
        }

        return (int)$value;
    }
}
