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
     * @param JobHandlerInterface $job
     */
    public function add(JobHandlerInterface $job)
    {
        $this->jobs[$job->listensToJob()] = $job;
    }

    /**
     * @param array $jobs
     * @return int count of added jobs
     */
    public function addMultipleJobs(array $jobs)
    {
        $count = 0;

        foreach ($jobs as $job) {
            if (!$job instanceof JobHandlerInterface) {
                continue;
            }

            $this->add($job);

            $count++;
        }

        return $count;
    }

    /**
     * @return JobHandlerInterface[]
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @param $jobName
     * @return JobHandlerInterface|null
     */
    public function getJob($jobName)
    {
        /** @var JobHandlerInterface $job  */
        foreach ($this->getJobs() as $job) {
            if ($job->listensToJob() === $jobName) {
                return $job;
            }
        }

        return null;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->jobs);
    }
}
