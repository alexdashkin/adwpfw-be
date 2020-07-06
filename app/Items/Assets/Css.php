<?php

namespace AlexDashkin\Adwpfw\Items\Assets;

/**
 * CSS file
 */
class Css extends Asset
{
    /**
     * Enqueue style
     */
    public function enqueue()
    {
        $callback = $this->get('callback');

        if ($callback && is_callable($callback) && !$callback()) {
            return;
        }

        $id = $this->get('prefix') . '-' . sanitize_title($this->get('id'));

        wp_enqueue_style($id, $this->get('url'), $this->get('deps'), $this->get('ver'));
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
                'default' => '',
            ],
            'deps' => [
                'type' => 'array',
                'default' => [],
            ],
            'callback' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];
    }
}
