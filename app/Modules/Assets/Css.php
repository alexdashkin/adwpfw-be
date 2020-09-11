<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

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
        $callback = $this->getProp('callback');

        if ($callback && is_callable($callback) && !$callback()) {
            return;
        }

        $id = $this->config('prefix') . '-' . sanitize_title($this->getProp('id'));

        wp_enqueue_style($id, $this->getProp('url'), $this->getProp('deps'), $this->getProp('ver'));
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
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
