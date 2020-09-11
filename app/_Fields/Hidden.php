<?php

namespace AlexDashkin\Adwpfw\_Fields;

/**
 * Hidden Field
 */
class Hidden extends Field
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
                    'default' => 'hidden',
                ],
                'sanitizer' => [
                    'type' => 'callable',
                    'default' => 'sanitize_text_field',
                ],
            ]
        );
    }
}
