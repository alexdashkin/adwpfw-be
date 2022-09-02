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
     * @return int
     */
    protected function sanitize($value): int
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
                'default' => 'number',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
