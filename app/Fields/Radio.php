<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Radio Field
 */
class Radio extends Field
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
                    'default' => 'radio',
                ],
                'options' => [
                    'type' => 'array',
                    'required' => true,
                ],
                'sanitizer' => [
                    'type' => 'callable',
                    'default' => 'sanitize_text_field',
                ],
            ]
        );
    }
}
