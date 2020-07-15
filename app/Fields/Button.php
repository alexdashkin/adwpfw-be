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
    protected function getInitialPropDefs(): array
    {
        return array_merge(
            parent::getInitialPropDefs(),
            [
                'tpl' => [
                    'default' => 'button',
                ],
            ]
        );
    }
}
