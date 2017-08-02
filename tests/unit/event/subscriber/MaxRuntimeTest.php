<?php
namespace tests\dmank\gearman\event\subscriber;

use dmank\gearman\event\subscriber\MaxRuntime;
use dmank\gearman\event\WorkerEvent;
use Psr\Log\LoggerInterface;
use dmank\gearman\Worker;

class MaxRuntimeTest extends \PHPUnit_Framework_TestCase
{
    public function testMaxRuntimeExceeded()
    {
        $worker = $this->getMockBuilder(Worker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $worker->expects($this->exactly(2))
            ->method('destroyWorker');

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $logger->expects($this->exactly(2))
            ->method('notice');

        $subscriber = new MaxRuntime('-1 second', $logger);

        $workerEvent = new WorkerEvent($worker);
        $subscriber->onAfterRun($workerEvent);
        $subscriber->onNoJobs($workerEvent);
    }

    public function testMaxRunTimeNotExceeded()
    {
        $subscriber = new MaxRuntime('+1 year');
        $worker = $this->getMockBuilder(Worker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $worker->expects($this->never())
            ->method('destroyWorker');

        $workerEvent = new WorkerEvent($worker);
        $subscriber->onAfterRun($workerEvent);
        $subscriber->onNoJobs($workerEvent);
    }

    public function testOnBeforeRunInform()
    {
        $worker = $this->getMockBuilder(Worker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $logger->expects($this->once())
            ->method('notice');

        $subscriber = new MaxRuntime('+1 year', $logger);
        $workerEvent = new WorkerEvent($worker);

        $subscriber->onBeforeRun($workerEvent);
    }
}
