<?php

declare(strict_types=1);

namespace Mzh\Helper;

class RunTimes
{
    var $StartTime = 0;
    var $StopTime = 0;

    function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }

    function start()
    {
        $this->StartTime = $this->get_microtime();
    }

    function stop()
    {
        $this->StopTime = $this->get_microtime();
    }

    function spent()
    {
        $this->StopTime = $this->get_microtime();
        return round(($this->StopTime - $this->StartTime) * 1000, 1) . '毫秒';
    }
}