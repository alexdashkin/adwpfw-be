<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

/**
 * type*, url*, id, ver, deps, callback, localize
 */
class Js extends Asset
{
    /**
     * Enqueue script
     */
    public function enqueue()
    {
        $callback = $this->getProp('callback');

        // Exit if callback returns false
        if ($callback && is_callable($callback) && !$callback()) {
            return;
        }

        $prefix = $this->prefix;

        $id = $this->getProp('id');

        // Enqueue already registered script and exit
/*        if (wp_script_is($id, 'registered')) {
            wp_enqueue_script($id);
            return;
        }*/

        $id = $prefix . '-' . $id;

        // Enqueue new script
        wp_enqueue_script($id, $this->getProp('url'), $this->getProp('deps'), $this->getProp('ver'), true);

        // Localize script
        $localize = array_merge(
            [
                'prefix' => $prefix,
                'nonce' => wp_create_nonce($prefix),
                'rest_nonce' => wp_create_nonce('wp_rest'),
                'ajax_url' => admin_url('admin-ajax.php'),
            ],
            $this->getProp('localize')
        );

        wp_localize_script($id, $prefix, $localize);
    }
}
