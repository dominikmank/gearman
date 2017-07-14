<?php
namespace dmank\gearman;

class Client
{
    /**
     * @var \GearmanClient
     */
    private $realClient;
    /**
     * @var ServerCollection
     */
    private $serverCollection;

    /**
     * @var bool
     */
    private $serverAddedToClient = false;

    const PRIORITY_LOW = 0;
    const PRIORITY_NORMAL = 1;
    const PRIORITY_HIGH = 2;

    /**
     * @param ServerCollection $servers
     */
    public function __construct(ServerCollection $servers)
    {
        $this->serverCollection = $servers;
    }

    /**
     * @param \GearmanClient $client
     */
    public function setImplementation(\GearmanClient $client)
    {
        $this->realClient = $client;
    }

    public function getJobStatus($jobHandle)
    {
        $status = $this->getClient()->jobStatus($jobHandle);

        return new JobStatus($status);
    }

    /**
     * @param     $method
     * @param     $workLoad
     * @param int $priority
     * @return mixed
     */
    public function executeInBackground($method, $workLoad, $priority = self::PRIORITY_LOW)
    {
        if (!StringHelper::isSerialized($workLoad)) {
            $workLoad = serialize($workLoad);
        }

        switch ($priority) {
            case self::PRIORITY_HIGH:
                $result = $this->getClient()->doHighBackground($method, $workLoad);
                break;
            case self::PRIORITY_NORMAL:
                $result = $this->getClient()->doBackground($method, $workLoad);
                break;
            case self::PRIORITY_LOW:
            default:
                $result = $this->getClient()->doLowBackground($method, $workLoad);
                break;
        }

        return $result;
    }


    /**
     * @param     $method
     * @param     $workLoad
     * @param int $priority
     * @return string
     */
    public function executeJob($method, $workLoad, $priority = self::PRIORITY_LOW)
    {
        if (!StringHelper::isSerialized($workLoad)) {
            $workLoad = serialize($workLoad);
        }

        $client = $this->getClient();

        switch ($priority) {
            case self::PRIORITY_HIGH:
                $result = $client->doHigh($method, $workLoad);
                break;
            case self::PRIORITY_NORMAL:
                $gearmanMethod = $this->getPriorityNormalMethod();
                $result = $client->$gearmanMethod($method, $workLoad);
                break;
            case self::PRIORITY_LOW:
            default:
                $result = $client->doLow($method, $workLoad);
                break;
        }

        return $result;
    }

    /**
     * @param array $jobs Array of dmank\gearman\Job, jobs to do
     * @param int   $priority
     * @return array
     */
    public function executeJobs(array $jobs, $priority = self::PRIORITY_LOW)
    {
        $client = $this->getClient();

        $results = [];
        $client->setCompleteCallback(function($task) use (&$results) {
            $results[] = $task->data();
        });

        foreach ($jobs as $job) {
            if (!is_a($job, Job::class)) {
                continue;
            }

            $client->addTask($job->getJobName(), $job->getWorkLoad());
        }

        $client->runTasks();

        return $results;
    }

    /**
     * @return \GearmanClient
     */
    private function getClient()
    {
        if (!$this->realClient) {
            $this->realClient = $this->createClient();
        }

        if (!$this->serverAddedToClient) {
            /* @var Server $server */
            foreach ($this->serverCollection->getServers() as $server) {
                $this->realClient->addServer($server->getHost(), $server->getPort());
            }

            $this->serverAddedToClient = true;
        }

        return $this->realClient;
    }

    /**
     * @return \GearmanClient
     */
    private function createClient()
    {
        $client = new \GearmanClient();

        return $client;
    }

    private function getPriorityNormalMethod()
    {
        $normalPriorityMethod = 'doLow';

        if (version_compare(phpversion('gearman'), '1.0.0') >= 0) {
            $normalPriorityMethod = 'doNormal';
        }

        return $normalPriorityMethod;
    }
}
