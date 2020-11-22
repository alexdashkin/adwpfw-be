<?php

namespace AlexDashkin\Adwpfw\Modules\Fields;

/**
 * multiple, options
 */
class Select extends Field
{
    /**
     * Prepare Template Args
     */
    protected function prepareArgs()
    {
        parent::prepareArgs();

        $args = $this->args;

        $value = $args['value'];
        $multiple = !empty($args['multiple']);
        $args['multiple'] = $multiple ? 'multiple' : '';

/*        if ($multiple) {
            $args['name'] .= '[]';
        }*/

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
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        $defaults = [
            'placeholder' => '--- Select ---',
            'multiple' => false,
            'options' => [],
        ];

        return array_merge(parent::defaults(), $defaults);
    }
}
