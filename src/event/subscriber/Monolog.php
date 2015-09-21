<?php
namespace dmank\gearman\event\subscriber;

use dmank\gearman\event\FunctionEvent;
use dmank\gearman\event\FunctionFailureEvent;
use dmank\gearman\event\RegisterFunctionEvent;
use dmank\gearman\event\WorkerEvent;
use dmank\gearman\event\WorkerExceptionEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Monolog implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            WorkerEvent::EVENT_BEFORE_RUN => array('onBeforeRun', 10),
            WorkerEvent::EVENT_ON_BEFORE_DESTROY => array('onBeforeDestroy', 10),
            WorkerEvent::EVENT_AFTER_RUN => array('onAfterRun', 10),
            WorkerEvent::EVENT_ON_IO_WAIT => array('onIOWait', 10),
            RegisterFunctionEvent::EVENT_ON_AFTER_REGISTER_FUNCTIONS => array('onAfterRegisterFunctions', 10),
            RegisterFunctionEvent::EVENT_ON_BEFORE_REGISTER_FUNCTIONS => array('onBeforeRegisterFunctions', 10),
            WorkerExceptionEvent::EVENT_ON_FAILURE => array('onFailure', 10),
            WorkerEvent::EVENT_ON_NO_JOBS => array('onNoJobs', 10),
            FunctionEvent::FUNCTION_BEFORE_EXECUTE => array('onFunctionBeforeExecute', 10),
            FunctionEvent::FUNCTION_AFTER_EXECUTE => array('onFunctionAfterExecute', 10),
            FunctionFailureEvent::FUNCTION_ON_FAILURE => array('onFunctionFailure', 10)
        );
    }

    public function onBeforeRun(WorkerEvent $event)
    {
        $this->logger->info('Starting Worker');
    }

    public function onAfterRun(WorkerEvent $event)
    {
        $this->logger->info('Finished work.');
    }

    public function onBeforeDestroy(WorkerEvent $event)
    {
        $this->logger->notice('Destroying worker.');
    }

    public function onIOWait(WorkerEvent $event)
    {
        $this->logger->debug('I`m idling.');
    }

    public function onFailure(WorkerExceptionEvent $event)
    {
        $this->logger->critical(
            sprintf('Something unexpected happened. Message was "%s"', $event->getException()->getMessage())
        );
    }

    public function onNoJobs(WorkerEvent $event)
    {
        $this->logger->debug(
            sprintf('Got no jobs to do.')
        );
    }

    public function onFunctionBeforeExecute(FunctionEvent $functionEvent)
    {
        $jobHandler = $functionEvent->getJobHandler();
        $job = $functionEvent->getJob();

        $this->logger->info(
            sprintf('Im starting working on job %s', $job->getJobName())
        );

        if ($jobHandler instanceof LoggerAwareInterface) {
            $this->logger->debug('Setting Logger to Job');
            $jobHandler->setLogger($this->logger);
        }
    }

    public function onFunctionAfterExecute(FunctionEvent $functionEvent)
    {
        $this->logger->info(
            sprintf('I successfully finished my work on job %s', $functionEvent->getJob()->getJobName())
        );
    }

    public function onFunctionFailure(FunctionFailureEvent $event)
    {
        $this->logger->critical(
            sprintf(
                'Something unexpected happened in job %s' .
                '... Message %s , Trace %s.',
                $event->getJob()->getJobName(),
                $event->getException()->getMessage(),
                $event->getException()->getTraceAsString()
            )
        );
    }

    public function onBeforeRegisterFunctions(RegisterFunctionEvent $registerFunctionEvent)
    {
        $this->logger->debug(
            sprintf(
                'I got %s jobs',
                $registerFunctionEvent->getJobs()->count()
            )
        );
    }

    public function onAfterRegisterFunctions(RegisterFunctionEvent $registerFunctionEvent)
    {
        $this->logger->debug(
            sprintf(
                'Added %s jobs to the Worker',
                $registerFunctionEvent->getJobs()->count()
            )
        );
    }
}
