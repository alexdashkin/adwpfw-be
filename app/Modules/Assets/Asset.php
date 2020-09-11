<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

use AlexDashkin\Adwpfw\Modules\Module;

abstract class Asset extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('admin' === $this->getProp('type') ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts', [$this, 'enqueue'], 99);
    }

    /**
     * Enqueue asset
     */
    abstract public function enqueue();
}
