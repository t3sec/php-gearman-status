<?php

namespace T3sec\GearmanStatus\Parser;


class GearmanParser
{

    public static function statusLine($line)
    {
        $lineMetrics = array();

        if (preg_match("~^(.*)[ \t](\d+)[ \t](\d+)[ \t](\d+)~", $line, $matches)) {
            $function = $matches[1];
            $lineMetrics[$function] = array(
                'name' => $function,
                'unfinished' => $matches[2],
                'running' => $matches[3],
                'workers' => $matches[4],
            );
        }

        return $lineMetrics;
    }

    public static function workersLine($line)
    {
        $lineMetrics = array();

        if (preg_match("~^(\d+)[ \t](.*?)[ \t](.*?) : ?(.*)~", $line, $matches)) {
            if (empty($matches[4])) {
                return;
            }
            $fd = $matches[1];
            $lineMetrics[$fd] = array(
                'name' => $matches[4],
                'ip' => $matches[2],
                'fd' => $matches[1],
                'id' => $matches[3],
            );
        }

        return $lineMetrics;
    }
}