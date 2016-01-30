<?php


namespace Extasy;


class EnvironmentDetector
{
    protected $map = null;

    public function __construct($map)
    {
        $this->map = $map;
    }

    public function detect($serverInfo)
    {
        foreach ($this->map as $env => $mappers) {


            foreach ($mappers as $map) {
                $found = true;
                foreach ( $map as $key=>$value) {
                    $keyMatches = isset($serverInfo[$key]) && ($value == $serverInfo[$key]);
                    if (!$keyMatches) {
                        $found = false;
                        break;
                    }
                }
                if ($found) {
                    return $env;
                }
            }
        }
        throw new \RuntimeException('Environment not detected');
    }
} 