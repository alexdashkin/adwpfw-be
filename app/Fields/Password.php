<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Password Field
 */
class Password extends Field
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
                    'default' => 'password',
                ],
            ]
        );
    }
}
