<?php


namespace Extasy\ORM;


class ArrayFactory
{
    protected $data = [];

    public function __construct($className, $data)
    {

        foreach ($data as $key => $row) {
            $data[$key] = new $className($row);
        }
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
} 