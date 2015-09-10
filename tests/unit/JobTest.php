<?php

namespace tests\dmank\gearman;

use dmank\gearman\Job;

class JobTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate()
    {
        $job = new Job('jobName', 'workloadAsString');

        $this->assertNotNull($job);

        $this->assertEquals('jobName', $job->getJobName());
        $this->assertEquals('workloadAsString', $job->getWorkLoad());
    }

    public function testSerializedWorkload()
    {
        $unserialized = array('foo' => 'bar');

        $job = new Job('jobName', serialize($unserialized));

        $this->assertNotNull($job);

        $this->assertEquals($unserialized, $job->getWorkLoad());
    }
}
