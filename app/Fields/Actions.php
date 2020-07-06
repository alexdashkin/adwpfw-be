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
    protected function props(): array
    {
        return array_merge(
            parent::props(),
            [
                'tpl' => [
                    'default' => 'actions',
                ],
            ]
        );
    }
}
