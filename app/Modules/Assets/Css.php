<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

/**
 * type*, url*, id, ver, deps, callback
 */
class Css extends Asset
{
    /**
     * Enqueue style
     */
    public function enqueue()
    {
        $callback = $this->getProp('callback');

        if ($callback && is_callable($callback) && !$callback()) {
            return;
        }

        $id = $this->prefix . '-' . sanitize_title($this->getProp('id'));

        wp_enqueue_style($id, $this->getProp('url'), $this->getProp('deps'), $this->getProp('ver'));
    }
}
