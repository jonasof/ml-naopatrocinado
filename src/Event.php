<?php

namespace ML_Encontra_Link;

use Sabre\Event\EventEmitter;

class Event
{
    public function __construct()
    {
        $this->eventEmitter = new EventEmitter();
    }

    public function listen($event, callable $callback)
    {
        $this->eventEmitter->on($event, $callback);
    }

    public function emit($event, $objects)
    {
        $this->eventEmitter->emit($event, $objects);
    }
}
