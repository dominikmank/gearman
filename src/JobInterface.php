<?php
namespace dmank\gearman;

interface JobInterface
{
    public function getJobName();
    public function getWorkLoad();
}
