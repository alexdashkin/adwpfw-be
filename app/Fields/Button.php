<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Button Field
 */
class Button extends Field
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
                    'default' => 'button',
                ],
            ]
        );
    }
}
