<?php

namespace dmank\gearman;

use dmank\gearman\event\ConnectToServerEvent;
use dmank\gearman\event\FunctionEvent;
use dmank\gearman\event\FunctionFailureEvent;
use dmank\gearman\event\RegisterFunctionEvent;
use dmank\gearman\event\WorkerEvent;
use dmank\gearman\event\WorkerExceptionEvent;
use dmank\gearman\exception\NoFunctionGiven;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Worker
{
    /**
     * @var \GearmanWorker
     */
    private $realWorker;
    /**
     * @var bool
     */
    private $connectedToServer = false;
    /**
     * @var bool
     */
    private $registeredFunctions = false;
    /**
     * @var ServerCollection
     */
    private $serverCollection;

    /**
     * @var JobCollection
     */
    private $jobs = [];

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var bool
     */
    private $killRequested = false;

    public function __construct(
        ServerCollection $servers,
        JobCollection $jobs,
        EventDispatcherInterface $dispatcher
    ) {
        $this->serverCollection = $servers;
        $this->jobs = $jobs;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param \GearmanWorker $worker
     */
    public function setImplementation(\GearmanWorker $worker)
    {
        $this->realWorker = $worker;
    }

    public function run()
    {
        $this->getEventDispatcher()->dispatch(
            WorkerEvent::EVENT_BEFORE_RUN,
            new WorkerEvent($this)
        );

        try {

            while ($this->workerIsPending()) {

                $gearmanReturnCode = $this->getWorker()->returnCode();

                if ($gearmanReturnCode == GEARMAN_IO_WAIT) {

                    $this->getEventDispatcher()->dispatch(
                        WorkerEvent::EVENT_ON_IO_WAIT,
                        new WorkerEvent($this)
                    );

                    @$this->getWorker()->wait();
                }

                if ($gearmanReturnCode == GEARMAN_NO_JOBS) {

                    $this->getEventDispatcher()->dispatch(
                        WorkerEvent::EVENT_ON_NO_JOBS,
                        new WorkerEvent($this)
                    );

                }

                if ($gearmanReturnCode != GEARMAN_SUCCESS) {

                    $this->getEventDispatcher()->dispatch(
                        WorkerEvent::EVENT_ON_WORK,
                        new WorkerEvent($this)
                    );
                }

                if ($gearmanReturnCode == GEARMAN_SUCCESS) {

                    $this->getEventDispatcher()->dispatch(
                        WorkerEvent::EVENT_AFTER_RUN,
                        new WorkerEvent($this)
                    );
                }

            }
        } catch (NoFunctionGiven $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->getEventDispatcher()->dispatch(
                WorkerExceptionEvent::EVENT_ON_FAILURE,
                new WorkerExceptionEvent($this, $e)
            );

        }
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return \GearmanWorker
     */
    private function getWorker()
    {
        if (!$this->realWorker) {
            $this->realWorker = new \GearmanWorker();
            $this->realWorker->setOptions(GEARMAN_WORKER_NON_BLOCKING);
        }

        if (!$this->connectedToServer) {

            $this->getEventDispatcher()->dispatch(
                ConnectToServerEvent::CONNECT_TO_SERVER_EVENT,
                new ConnectToServerEvent($this->serverCollection)
            );

            /* @var \dmank\gearman\Server $server */
            foreach ($this->serverCollection->getServers() as $server) {
                $this->realWorker->addServer($server->getHost(), $server->getPort());
            }

            $this->getEventDispatcher()->dispatch(
                ConnectToServerEvent::CONNECTED_TO_SERVER_EVENT,
                new ConnectToServerEvent($this->serverCollection)
            );

            $this->connectedToServer = true;
        }

        if (!$this->registeredFunctions) {
            $this->registerFunctions();

            $this->registeredFunctions = true;
        }

        return $this->realWorker;
    }

    private function registerFunctions()
    {
        $this->getEventDispatcher()->dispatch(
            RegisterFunctionEvent::EVENT_ON_BEFORE_REGISTER_FUNCTIONS,
            new RegisterFunctionEvent($this->jobs)
        );

        if (count($this->jobs) == 0) {

            throw new NoFunctionGiven(
                sprintf('Didnt have jobs to register. So we need to stop here. My bad!')
            );
        }

        /* @var JobHandlerInterface $jobClass */
        foreach ($this->jobs->getJobs() as $jobName => $jobClass) {
            $this->realWorker->addFunction(
                $jobName,
                function (\GearmanJob $gearmanJob) use ($jobClass, $jobName) {

                    $job = new Job($jobName, $gearmanJob->workload());

                    try {

                        $this->getEventDispatcher()->dispatch(
                            FunctionEvent::FUNCTION_BEFORE_EXECUTE,
                            new FunctionEvent($jobClass, $job)
                        );

                        $result = $jobClass->execute($job);

                        $this->getEventDispatcher()->dispatch(
                            FunctionEvent::FUNCTION_AFTER_EXECUTE,
                            new FunctionEvent($jobClass, $job, $result)
                        );
                    } catch (\Exception $e) {

                        $this->getEventDispatcher()->dispatch(
                            FunctionFailureEvent::FUNCTION_ON_FAILURE,
                            new FunctionFailureEvent($jobClass, $e, $job)
                        );

                        $result = $e;
                    }

                    $gearmanJob->sendComplete($result);
                }
            );
        }

        $this->getEventDispatcher()->dispatch(
            RegisterFunctionEvent::EVENT_ON_AFTER_REGISTER_FUNCTIONS,
            new RegisterFunctionEvent($this->jobs)
        );

    }

    /**
     * @return bool
     */
    private function workerIsPending()
    {
        return (
            $this->getWorker()->work() ||
            $this->getWorker()->returnCode() == GEARMAN_IO_WAIT ||
            $this->getWorker()->returnCode() == GEARMAN_NO_JOBS
        ) && !$this->killRequested;
    }

    public function destroyWorker()
    {
        $this->getEventDispatcher()->dispatch(
            WorkerEvent::EVENT_ON_BEFORE_DESTROY,
            new WorkerEvent($this)
        );

        $this->killRequested = true;
    }
}
