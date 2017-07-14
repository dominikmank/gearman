<?php
namespace tests\dmank\gearman;

use dmank\gearman\Client;
use dmank\gearman\JobStatus;
use dmank\gearman\Job;
use dmank\gearman\Server;
use dmank\gearman\ServerCollection;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\GearmanClient
     */
    private $mockedImplementation;

    public function setUp()
    {
        parent::setUp();

        $this->mockedImplementation = $this->getMockBuilder('\GearmanClient')->getMock();
    }

    public function testImplementation()
    {
        $serverCollection = new ServerCollection();
        $client = new Client($serverCollection);

        $this->assertNotNull($client, 'Client must be instantiated');
    }

    /**
     * @dataProvider asyncProvider
     */
    public function testAsyncExecution($method, $workload, $priority, $implementationName)
    {
        $serverCollection = new ServerCollection();
        $client = new Client($serverCollection);
        $client->setImplementation($this->mockedImplementation);


        $this->mockedImplementation->expects($this->once())
            ->method($implementationName)
            ->with($method, serialize($workload))
            ->will($this->returnValue('irgendwas'));

        $client->executeInBackground($method, $workload, $priority);
    }

    public function testGetJobStatus()
    {
        $serverCollection = new ServerCollection();
        $client = new Client($serverCollection);
        $client->setImplementation($this->mockedImplementation);

        $this->mockedImplementation->expects($this->once())
            ->method('jobStatus')
            ->with('jobHandle')
            ->will($this->returnValue(array(0, 0, 0, 0)));

        $status = $client->getJobStatus('jobHandle');

        $this->assertEquals(new JobStatus(array(0, 0, 0, 0)), $status);
    }

    /**
     * @dataProvider promptProvider
     */
    public function testPromptExecution($method, $workload, $priority, $implementationName)
    {
        $serverCollection = new ServerCollection();
        $client = new Client($serverCollection);
        $client->setImplementation($this->mockedImplementation);


        $this->mockedImplementation->expects($this->once())
            ->method($implementationName)
            ->with($method, serialize($workload))
            ->will($this->returnValue('irgendwas'));

        $client->executeJob($method, $workload, $priority);
    }

    public function testAddServerToClient()
    {
        $server = new Server();
        $serverCollection = $this->getMockBuilder('\dmank\gearman\ServerCollection')->getMock();
        $serverCollection->expects($this->once())
            ->method('getServers')
            ->will($this->returnValue(array($server)));

        $this->mockedImplementation->expects($this->once())
            ->method('addServer')
            ->with($server->getHost(), $server->getPort())
            ->will($this->returnValue(true));

        $client = new Client($serverCollection);
        $client->setImplementation($this->mockedImplementation);

        $client->executeJob('foo', 'bar');
    }

    public function testExecuteJobs()
    {
        $server = new Server();
        $serverCollection = $this->getMockBuilder('\dmank\gearman\ServerCollection')->getMock();
        $serverCollection->expects($this->once())
            ->method('getServers')
            ->will($this->returnValue(array($server)));

        $job = new Job('JobName', 'WorkLoads');
        $this->mockedImplementation->expects($this->once())
            ->method('addTask')
            ->with($job->getJobName(), $job->getWorkLoad());
        $this->mockedImplementation->expects($this->once())
            ->method('runTasks');

        $client = new Client($serverCollection);
        $client->setImplementation($this->mockedImplementation);

        $jobs = [];
        $jobs[] = $job;
        $jobs[] = 'Something can not be accpeted';

        $client->executeJobs($jobs);
    }

    public function asyncProvider()
    {
        return array(
            'low' => array('method', 'workload', Client::PRIORITY_LOW, 'doLowBackground'),
            'normal' => array('method', 'workload', Client::PRIORITY_NORMAL, 'doBackground'),
            'high' => array('method', 'workload', Client::PRIORITY_HIGH, 'doHighBackground')
        );
    }

    public function promptProvider()
    {
        $methodName = 'doLow';
        if (version_compare(phpversion('gearman'), '1.0.0') >= 0) {
            $methodName = 'doNormal';
        }

        return array(
            'low' => array('method', 'workload', Client::PRIORITY_LOW, 'doLow'),
            'normal' => array('method', 'workload', Client::PRIORITY_NORMAL, $methodName),
            'high' => array('method', 'workload', Client::PRIORITY_HIGH, 'doHigh')
        );
    }
}
