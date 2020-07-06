<?php

namespace AlexDashkin\Adwpfw\Fields;

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
    protected function props(): array
    {
        return array_merge(
            parent::props(),
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
