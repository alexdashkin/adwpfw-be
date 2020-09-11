<?php

namespace AlexDashkin\Adwpfw\_Fields;

/**
 * Number Field
 */
class Number extends Field
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
                    'default' => 'number',
                ],
                'sanitizer' => [
                    'type' => 'callable',
                    'default' => 'intval',
                ],
                'min' => [
                    'type' => 'int',
                    'default' => 0,
                ],
                'max' => [
                    'type' => 'int',
                    'default' => PHP_INT_MAX,
                ],
                'step' => [
                    'type' => 'int',
                    'default' => 1,
                ],
            ]
        );
    }
}
