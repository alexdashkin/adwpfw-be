<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Textarea Field
 */
class Textarea extends Field
{
    /**
     * Sanitize field value on save
     *
     * @param mixed $value
     * @return string
     */
    protected function sanitize($value): string
    {
        return sanitize_textarea_field($value);
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
            'template' => [
                'type' => 'string',
                'default' => 'textarea',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
