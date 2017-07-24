<?php
namespace dmank\gearman;

interface JobHandlerInterface
{
    /**
     * @param JobInterface $job
     * @return mixed
     */
    public function execute(JobInterface $job);
}
