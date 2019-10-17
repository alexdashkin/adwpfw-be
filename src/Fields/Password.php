<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Password Field.
 */
class Password extends Text
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Default 'password'.
     * @type string $class CSS Class(es) for the control. Default 'adwpfw-form-control'.
     * @type string $label Label.
     * @type string $desc Description.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'password',
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }
}
