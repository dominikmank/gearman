<?php
namespace dmank\gearman;

class JobCollection implements \Countable
{
    /**
     * @var array
     */
    private $jobs;

    public function __construct()
    {
        $this->jobs = array();
    }

    /**
     * @param $jobName
     * @param JobHandler $job
     */
    public function add($jobName, JobHandler $job)
    {
        $this->jobs[$jobName] = $job;
    }

    /**
     * @param array $jobs
     * @return int count of added jobs
     */
    public function addMultipleJobs(array $jobs)
    {
        $count = 0;

        foreach ($jobs as $jobName => $job) {
            if (!$job instanceof JobHandler) {
                continue;
            }

            $this->add($jobName, $job);

            $count++;
        }

        return $count;
    }

    /**
     * @return array of JobHandler
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->jobs);
    }
}
