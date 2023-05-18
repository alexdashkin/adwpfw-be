<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * HTML List Field
 */
class HTMLList extends Field
{
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
                'default' => 'list',
            ],
        ];
        
        return array_merge($baseProps, $fieldProps);
    }
}
