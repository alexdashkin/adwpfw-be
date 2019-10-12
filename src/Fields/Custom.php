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
     * @param array $data
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        parent::__construct($data, $props);
    }
}
