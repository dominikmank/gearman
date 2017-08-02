<?php
namespace tests\dmank\gearman;

use dmank\gearman\JobCollection;
use dmank\gearman\Server;
use dmank\gearman\ServerCollection;
use dmank\gearman\Worker;
use Symfony\Component\EventDispatcher\EventDispatcher;
use dmank\gearman\JobHandlerInterface;

class WorkerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\GearmanWorker
     */
    private $workerImplementation;

    /**
     * @var \dmank\gearman\JobHandlerInterface
     */
    private $jobHandler;

    public function setUp()
    {
        parent::setUp();

        $this->workerImplementation = $this->getMockBuilder(\GearmanWorker::class)
            ->setMethods(array('addFunction', 'addServer', 'work'))->getMock();

        $this->jobHandler = $this->getMockBuilder(JobHandlerInterface::class)->getMock();

    }

    public function testListenToServer()
    {
        $serverCollection = new ServerCollection();
        $serverCollection->add(new Server());

        $this->workerImplementation->expects($this->once())
            ->method('addServer');
        $this->workerImplementation->expects($this->once())
            ->method('addFunction');

        $jobCollection = new JobCollection();
        $jobCollection->add($this->jobHandler);

        $worker = new Worker($serverCollection, $jobCollection, new EventDispatcher());
        $worker->setImplementation($this->workerImplementation);

        $worker->run();
    }

    /**
     * @expectedException \dmank\gearman\exception\NoFunctionGiven
     */
    public function testRegisterNoFunctionsThrowsException()
    {
        $serverCollection = new ServerCollection();
        $serverCollection->add(new Server());

        $this->workerImplementation->expects($this->once())
            ->method('addServer');

        $jobCollection = new JobCollection();

        $worker = new Worker($serverCollection, $jobCollection, new EventDispatcher());
        $worker->setImplementation($this->workerImplementation);

        $worker->run();
    }

    public function testRegisterFunction()
    {
        $this->jobHandler->expects($this->exactly(2))
            ->method('listensToJob')
            ->willReturnOnConsecutiveCalls(
                'foo',
                'baz'
            );

        $jobCollection = new JobCollection();
        $jobCollection->add($this->jobHandler);
        $jobCollection->add($this->jobHandler);




        $serverCollection = new ServerCollection();
        $serverCollection->add(new Server());
        $worker = new Worker($serverCollection, $jobCollection, new EventDispatcher());
        $worker->setImplementation($this->workerImplementation);

        $this->workerImplementation->expects($this->exactly($jobCollection->count()))
            ->method('addFunction');

        $this->workerImplementation->expects($this->at(1))
            ->method('addFunction')
            ->with('foo', function(){}, null, null)
            ->will($this->returnValue(true));
        $this->workerImplementation->expects($this->at(2))
            ->method('addFunction')
            ->with('baz', function(){}, null, null)
            ->will($this->returnValue(true));

        $worker->run();
    }
}
