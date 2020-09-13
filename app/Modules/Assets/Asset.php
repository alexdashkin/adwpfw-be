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
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'id' => function () {
                return sanitize_key(str_replace(' ', '_', $this->getProp('type')));
            },
        ];
    }
    /**
     * Enqueue asset
     */
    abstract public function enqueue();
}
