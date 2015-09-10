<?php
namespace dmank\gearman\event;

use dmank\gearman\JobCollection;
use Symfony\Component\EventDispatcher\Event;

class RegisterFunctionEvent extends Event
{
    const EVENT_ON_BEFORE_REGISTER_FUNCTIONS = 'worker.on_before_register_functions';
    const EVENT_ON_AFTER_REGISTER_FUNCTIONS = 'worker.on_after_register_functions';

    private $jobs;

    public function __construct(JobCollection $jobs)
    {
        $this->jobs = $jobs;
    }

    public function getJobs()
    {
        return $this->jobs;
    }
}
