<?php

namespace AlexDashkin\Adwpfw\_Fields;

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
    protected function getInitialPropDefs(): array
    {
        return array_merge(
            parent::getInitialPropDefs(),
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
