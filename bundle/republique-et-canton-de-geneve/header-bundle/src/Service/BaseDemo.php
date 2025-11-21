<?php

namespace Geneve\HeaderBundle\Service;

class BaseDemo
{
    public function __construct(private string $param1, private string $param2, private string $param3 )
    {
    }

    public function info(): string
    {
        return "BaseDemo service with param1={$this->param1}, param2={$this->param2}, param3={$this->param3}";
    }
}