<?php
namespace dmank\gearman;

interface JobHandler
{
    /**
     * @param Job $job
     * @return mixed
     */
    public function execute(Job $job);
}
