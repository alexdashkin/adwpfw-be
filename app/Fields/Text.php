<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Text/Email/Search Field
 */
class Text extends Field
{
    /**
     * Sanitize field value on save
     *
     * @param mixed $value
     * @return mixed
     */
    protected function sanitize($value)
    {
        return sanitize_text_field($value);
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        $baseProps = parent::getPropDefs();

        $fieldProps = [
            'type' => [
                'type' => 'string',
                'default' => 'text',
            ],
            'template' => [
                'type' => 'string',
                'default' => 'text',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
