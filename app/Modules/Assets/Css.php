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
        $callback = $this->gp('callback');

        if ($callback && is_callable($callback) && !$callback()) {
            return;
        }

        $id = $this->gp('prefix') . '-' . sanitize_title($this->gp('id'));

        wp_enqueue_style($id, $this->gp('url'), $this->gp('deps'), $this->gp('ver'));
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
