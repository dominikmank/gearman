<?php
namespace dmank\gearman;

class StringHelper
{
    static public function isSerialized($string)
    {
        $test = @unserialize($string);
        return ($string === 'b:0;' || $test !== false);
    }
}
