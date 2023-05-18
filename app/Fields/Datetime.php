<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Date Field
 */
class Datetime extends Field
{
    /**
     * Sanitize field value on save
     *
     * @param mixed $value
     * @return string
     */
    protected function sanitize($value): string
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
            'template' => [
                'type' => 'string',
                'default' => 'datetime',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
