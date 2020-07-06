<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Custom Field
 */
class Custom extends Field
{
    /**
     * Get Field props
     *
     * @return array
     */
    protected function props(): array
    {
        return array_merge(
            parent::props(),
            [
                'tpl' => [
                    'default' => 'custom',
                ],
            ]
        );
    }
}
