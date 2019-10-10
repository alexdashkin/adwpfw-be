<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Form Field
 */
class Html extends Field
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $content Required.
     * }
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'html',
            ],
            'content' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, array_merge($props, $defaults));
    }
}
