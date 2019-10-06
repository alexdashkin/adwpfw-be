<?php

namespace AlexDashkin\Adwpfw\Modules;

abstract class Base
{
    protected $app;
    protected $config;

    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app->config;
    }

    protected function m($module)
    {
        return $this->app->m($module);
    }

    protected function log($message)
    {
        return $this->m('Logger')->log($message);
    }
}