<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Select field
 */
class Select extends Field
{
    /**
     * Prepare Template Args
     *
     * @param int $objectId
     */
    protected function prepareArgs(int $objectId = 0)
    {
        parent::prepareArgs($objectId);

        $args = $this->args;

        $value = $args['value'];
        $multiple = !empty($args['multiple']);
        $args['multiple'] = $multiple ? 'multiple' : '';

        $options = [];

        if (!empty($args['placeholder']) && !$multiple) {
            $options = [
                [
                    'label' => $args['placeholder'],
                    'value' => '',
                    'selected' => '',
                ]
            ];
        }

        foreach ($args['options'] ?? [] as $val => $label) {
            $selected = $multiple ? in_array($val, (array)$value) : $val == $value;

            $options[] = [
                'label' => $label,
                'value' => $val,
                'selected' => $selected ? 'selected' : '',
            ];
        }

        $args['options'] = $options;

        $this->args = $args;
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
            'options' => [
                'type' => 'array',
                'required' => true,
            ],
            'placeholder' => [
                'type' => 'string',
                'default' => '--- Select ---',
            ],
            'multiple' => [
                'type' => 'bool',
                'default' => false,
            ],
            'template' => [
                'type' => 'string',
                'default' => 'fields/select',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
