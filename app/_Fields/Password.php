<?php

namespace AlexDashkin\Adwpfw\_Fields;

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
    protected function getInitialPropDefs(): array
    {
        return array_merge(
            parent::getInitialPropDefs(),
            [
                'tpl' => [
                    'default' => 'password',
                ],
            ]
        );
    }
}
