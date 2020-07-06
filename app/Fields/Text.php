<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Text Field
 */
class Text extends Field
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
                    'default' => 'text',
                ],
                'sanitizer' => [
                    'type' => 'callable',
                    'default' => 'sanitize_text_field',
                ],
            ]
        );
    }
}
