<?php

namespace AlexDashkin\Adwpfw\Items\Assets;

use AlexDashkin\Adwpfw\Abstracts\Module;

abstract class Asset extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('admin' === $this->get('type') ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts', [$this, 'enqueue']);
    }

    /**
     * Enqueue asset
     */
    abstract public function enqueue();
}
