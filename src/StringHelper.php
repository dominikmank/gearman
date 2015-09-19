<?php
namespace dmank\gearman;

class StringHelper
{
    public static function isSerialized($string)
    {
        $test = @unserialize($string);
        return ($string === 'b:0;' || $test !== false);
    }
}
