<?php

namespace AlexDashkin\Adwpfw\Items\Assets;

/**
 * CSS file
 */
class Js extends Asset
{
    /**
     * Enqueue script
     */
    public function enqueue()
    {
        $callback = $this->get('callback');

        // Exit if callback returns false
        if ($callback && is_callable($callback) && !$callback()) {
            return;
        }

        $prefix = $this->get('prefix');

        $id = sanitize_title($this->get('id'));

        // Enqueue already registered script and exit
        if (wp_script_is($id, 'registered')) {
            wp_enqueue_script($id);
            return;
        }

        $id = $prefix . '-' . $id;

        // Enqueue new script
        wp_enqueue_script($id, $this->get('url'), $this->get('deps'), $this->get('ver'), true);

        // Localize script
        $localize = array_merge(
            [
                'prefix' => $prefix,
                'nonce' => wp_create_nonce($prefix),
                'rest_nonce' => wp_create_nonce('wp_rest'),
                'ajax_url' => admin_url('admin-ajax.php'),
            ],
            $this->get('localize')
        );

        wp_localize_script($id, $prefix, $localize);
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'type' => [
                'required' => true,
            ],
            'url' => [
                'required' => true,
            ],
            'id' => [
                'default' => function ($data) {
                    return $data['type'];
                },
            ],
            'ver' => [
                // To bypass trim() as string
                'type' => 'null',
                'default' => null,
            ],
            'deps' => [
                'type' => 'array',
                'default' => [],
            ],
            'callback' => [
                'type' => 'callable',
                'default' => null,
            ],
            'localize' => [
                'type' => 'array',
                'default' => [],
            ],
        ];
    }
}
