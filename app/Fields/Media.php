<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * WP Media Field
 */
class Media extends Field
{
    /**
     * Sanitize field value on save
     *
     * @param mixed $value
     * @return string
     */
    protected function sanitize($value): string
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
            'showUrl' => [
                'type' => 'bool',
                'default' => false,
            ],
            'template' => [
                'type' => 'string',
                'default' => 'media',
            ],
        ];
        
        return array_merge($baseProps, $fieldProps);
    }
}
