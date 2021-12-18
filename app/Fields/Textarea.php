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
     * @return mixed
     */
    protected function sanitize($value)
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
                'default' => 'fields/textarea',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
