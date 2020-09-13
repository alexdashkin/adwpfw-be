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
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        switch ($key) {
            case 'id':
                return sanitize_key(str_replace(' ', '_', $this->getProp('type')));
        }

        return null;
    }

    /**
     * Enqueue asset
     */
    abstract public function enqueue();
}
