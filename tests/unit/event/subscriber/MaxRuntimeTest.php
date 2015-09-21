<?php
namespace tests\dmank\gearman\event\subscriber;

use dmank\gearman\event\subscriber\MaxRuntime;
use dmank\gearman\event\WorkerEvent;

class MaxRuntimeTest extends \PHPUnit_Framework_TestCase
{
    public function testMaxRuntimeExceeded()
    {
        $worker = $this->getMockBuilder('dmank\gearman\Worker')
            ->disableOriginalConstructor()
            ->getMock();

        $worker->expects($this->exactly(2))
            ->method('destroyWorker');

        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

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
        $worker = $this->getMockBuilder('dmank\gearman\Worker')
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
        $worker = $this->getMockBuilder('dmank\gearman\Worker')
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $logger->expects($this->once())
            ->method('notice');

        $subscriber = new MaxRuntime('+1 year', $logger);
        $workerEvent = new WorkerEvent($worker);

        $subscriber->onBeforeRun($workerEvent);
    }
}
