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
    protected function props(): array
    {
        return array_merge(
            parent::props(),
            [
                'tpl' => [
                    'default' => 'heading',
                ],
            ]
        );
    }
}