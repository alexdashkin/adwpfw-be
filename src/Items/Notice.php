<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Admin Notice.
 */
class Notice extends Item
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Defaults to sanitized 'notice'.
     * @type string $message Message to display (tpl will be ignored). Default empty.
     * @type string $tpl Name of the notice Twig template. Default empty.
     * @type string $type Notice type (success, error). Default 'success'.
     * @type bool $dismissible Whether can be dismissed. Default true.
     * @type int $days When to show again after dismissed. Default 0.
     * @type array $classes Container CSS classes. Default empty.
     * @type array $args Additional Twig args. Default empty.
     * @type callable $callback Must return true for the Notice to show. Default empty.
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId('notice'),
            ],
            'message' => [
                'default' => null,
            ],
            'tpl' => [
                'default' => null,
            ],
            'type' => [
                'default' => 'success'
            ],
            'dismissible' => [
                'type' => 'bool',
                'default' => true,
            ],
            'days' => [
                'type' => 'int',
                'default' => 0,
            ],
            'classes' => [
                'type' => 'array',
                'default' => [],
            ],
            'args' => [
                'type' => 'array',
                'default' => [],
            ],
            'callback' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];

        parent::__construct($app, $data, $props);
    }

    /**
     * Show the Notice.
     */
    public function show()
    {
        $this->updateOption($this->data['id'], 0);
    }

    /**
     * Stop Showing the Notice.
     */
    public function stop()
    {
        $this->updateOption($this->data['id'], 2147483647);
    }

    /**
     * Dismiss the Notice.
     */
    public function dismiss()
    {
        $this->updateOption($this->data['id'], time());
    }

    /**
     * Process the Notice.
     */
    public function process()
    {
        $data = $this->data;

        if ($data['callback'] && !$data['callback']()) {
            return;
        }

        $optionName = $this->prefix . '_notices';
        $optionValue = get_option($optionName) ?: [];
        $dismissed = !empty($optionValue[$data['id']]) ? $optionValue[$data['id']] : 0;

        // If dismissed but days have not yet passed - do not show
        if ($dismissed > time() - $data['days'] * DAY_IN_SECONDS) {
            return;
        }

        echo $this->render();
    }

    /**
     * Render the Notice.
     *
     * @return string
     */
    private function render()
    {
        $data = $this->data;

        $id = $data['id'];

        $classes = $this->prefix . '-notice ' . implode(' ', $data['classes']) . ' notice notice-' . $data['type'];

        if ($data['dismissible']) {
            $classes .= ' is-dismissible';
        }

        if ($data['message']) {
            return "<div class='$classes' data-id='$id'><p>{$data['message']}</p></div>";

        } elseif ($data['tpl']) {
            $data['args']['id'] = $id;
            $data['args']['classes'] = $classes;

            return $this->m('Twig')->renderFile('notices/' . $data['tpl'], $data['args']);
        }

        return '';
    }

    /**
     * Update Notices option.
     *
     * @param string $name Param name
     * @param mixed $value Value
     */
    private function updateOption($name, $value)
    {
        $optionName = $this->prefix . '_notices';

        $optionValue = get_option($optionName) ?: [];

        $optionValue[$name] = $value;

        update_option($optionName, $optionValue);
    }
}
