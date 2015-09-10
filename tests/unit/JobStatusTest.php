<?php
namespace tests\dmank\gearman;

use dmank\gearman\JobStatus;

class JobStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return bool
     */
    public function testIsKnown()
    {
        $statusFalse = new JobStatus(array(0, 0, 0, 0));
        $statusTrue = new JobStatus(array(1, 0, 0, 0));

        $this->assertFalse($statusFalse->isKnown());
        $this->assertTrue($statusTrue->isKnown());
    }

    /**
     * @return bool
     */
    public function testIsRunning()
    {
        $statusFalse = new JobStatus(array(0, 0, 0, 0));
        $statusTrue = new JobStatus(array(0, 1, 0, 0));

        $this->assertFalse($statusFalse->isRunning());
        $this->assertTrue($statusTrue->isRunning());
    }

    /**
     * @return bool
     */
    public function testIsCompleted()
    {
        $statusFalse = new JobStatus(array(1, 1, 0, 0));
        $statusTrue = new JobStatus(array(0, 0, 0, 0));

        $this->assertFalse($statusFalse->isCompleted());
        $this->assertTrue($statusTrue->isCompleted());
    }
}
