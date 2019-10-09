<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;

/**
 * Form Field
 */
class Textarea extends Text
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $desc Field Description
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->tpl = 'textarea';

        parent::__construct($data, $app);
    }
}
