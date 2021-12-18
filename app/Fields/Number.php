<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Text/Email/Search Field
 */
class Number extends Field
{
    /**
     * Sanitize field value on save
     *
     * @param mixed $value
     * @return mixed
     */
    protected function sanitize($value)
    {
        return (int)$value;
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
            'min' => [
                'type' => 'int',
                'default' => 0,
            ],
            'max' => [
                'type' => 'int',
            ],
            'step' => [
                'type' => 'int',
            ],
            'template' => [
                'type' => 'string',
                'default' => 'fields/number',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
