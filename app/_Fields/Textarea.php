<?php

namespace AlexDashkin\Adwpfw\_Fields;

/**
 * Textarea Field
 */
class Textarea extends Field
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
                    'default' => 'textarea',
                ],
                'sanitizer' => [
                    'type' => 'callable',
                    'default' => 'sanitize_textarea_field',
                ],
            ]
        );
    }
}
