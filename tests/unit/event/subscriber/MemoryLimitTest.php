<?php
namespace tests\dmank\gearman\event\subscriber;

use dmank\gearman\event\subscriber\MemoryLimit;
use dmank\gearman\event\WorkerEvent;
use dmank\gearman\Worker;

class MemoryLimitTest extends \PHPUnit_Framework_TestCase
{
    public function testMemoryLimitExceededOnAfterRun()
    {
        $subscriber = new MemoryLimit('1k');

        $worker = $this->getMockBuilder(Worker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $worker->expects($this->exactly(1))
            ->method('destroyWorker');

        $workerEvent = new WorkerEvent($worker);
        $subscriber->onAfterRun($workerEvent);
    }

    public function testMemoryLimitNotExceededOnAfterRun()
    {
        $subscriber = new MemoryLimit('10g');

        $worker = $this->getMockBuilder(Worker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $worker->expects($this->exactly(0))
            ->method('destroyWorker');

        $workerEvent = new WorkerEvent($worker);
        $subscriber->onAfterRun($workerEvent);
    }
}
