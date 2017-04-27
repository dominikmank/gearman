<?php
namespace dmank\gearman;

interface JobHandlerInterface
{
    /**
     * @param Job $job
     * @return mixed
     */
    public function execute(Job $job);
}
