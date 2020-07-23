<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Actions Field
 */
class Actions extends Select
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
                    'default' => 'actions',
                ],
                'class' => [
                    'default' => '',
                ],
            ]
        );
    }
}
