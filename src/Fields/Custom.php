<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Custom Field.
 */
class Custom extends Field
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Required.
     * @type string $class CSS Class(es) for the control. Default empty.
     * @type string $label Label.
     * @type string $desc Description.
     * }
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        parent::__construct($data, $props);
    }
}
