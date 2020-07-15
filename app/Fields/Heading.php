<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Heading Field
 */
class Heading extends Field
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
                    'default' => 'heading',
                ],
            ]
        );
    }
}
