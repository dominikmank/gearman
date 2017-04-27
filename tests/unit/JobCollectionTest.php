<?php
namespace tests\dmank\gearman;

use dmank\gearman\JobCollection;

class JobCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddSingeJob()
    {
        $jobCollection = new JobCollection();
        $jobHandlerMock = $this->getMockBuilder('\dmank\gearman\JobHandlerInterface')->getMock();

        $this->assertEquals(0, $jobCollection->count());
        $this->assertCount(0, $jobCollection->getJobs());

        $jobCollection->add('testJob', $jobHandlerMock);

        $this->assertEquals(1, $jobCollection->count(), 'after adding there should be 1 job');
        $this->assertCount(1, $jobCollection->getJobs(), 'after adding there should be 1 job');
    }

    public function testAddMultipleJobs()
    {
        $jobCollection = new JobCollection();
        $jobHandlerMock = $this->getMockBuilder('\dmank\gearman\JobHandlerInterface')->getMock();
        $jobHandlerMock2 = $this->getMockBuilder('\dmank\gearman\JobHandlerInterface')->getMock();

        $resultAdded = $jobCollection->addMultipleJobs(
            array('jobName1' => $jobHandlerMock, 'jobName2' => $jobHandlerMock2)
        );

        $this->assertEquals(2, $resultAdded);
        $this->assertEquals(2, $jobCollection->count());
    }

    public function testAddMultipleWithIllegalClass()
    {
        $jobCollection = new JobCollection();

        $result = $jobCollection->addMultipleJobs(array('jobName' => new \stdClass()));

        $this->assertEquals(0, $result, 'there should be no result, since its not added to the collection.');
        $this->assertEquals(
            0,
            $jobCollection->count(),
            'there should be no result, since its not added to the collection.'
        );
    }

    public function testOverrideJob()
    {
        $jobCollection = new JobCollection();
        $jobHandlerMock = $this->getMockBuilder('\dmank\gearman\JobHandlerInterface')->getMock();
        $jobHandlerMock2 = $this->getMockBuilder('\dmank\gearman\JobHandlerInterface')->getMock();

        $jobCollection->add('testJob', $jobHandlerMock);
        $jobCollection->add('testJob', $jobHandlerMock2);

        $this->assertEquals(
            1,
            $jobCollection->count(),
            'we added 2 jobs with the same name, so there should be win the "last"'
        );
        $this->assertCount(
            1,
            $jobCollection->getJobs(),
            'we added 2 jobs with the same name, so there should be win the "last"'
        );
    }
}
