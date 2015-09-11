<?php
namespace tests\dmank\gearman\event\subscriber;

use dmank\gearman\event\subscriber\MaxRuntime;
use dmank\gearman\event\WorkerEvent;

class MaxRuntimeTest extends \PHPUnit_Framework_TestCase
{
    public function testMaxRuntimeExceeded()
    {
        $subscriber = new MaxRuntime('-1 second');
        $worker = $this->getMockBuilder('dmank\gearman\Worker')
            ->disableOriginalConstructor()
            ->getMock();

        $worker->expects($this->exactly(2))
            ->method('destroyWorker');

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
}
