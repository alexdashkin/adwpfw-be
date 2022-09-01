<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Checkbox field
 */
class Checkbox extends Field
{
    /**
     * Prepare Template Args
     *
     * @param int $objectId
     */
    protected function prepareArgs(int $objectId = 0)
    {
        parent::prepareArgs($objectId);

        $this->args['checked'] = !empty($this->args['value']) ? 'checked' : '';
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
            'checked' => [
                'type' => 'bool',
                'default' => false,
            ],
            'template' => [
                'type' => 'string',
                'default' => 'checkbox',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
