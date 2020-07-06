<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Select Field
 */
class Select extends Field
{
    /**
     * Get Field props
     *
     * @return array
     */
    protected function props(): array
    {
        return array_merge(
            parent::props(),
            [
                'tpl' => [
                    'default' => 'select',
                ],
                'options' => [
                    'type' => 'array',
                    'required' => true,
                ],
                'placeholder' => [
                    'default' => '--- Select ---',
                ],
                'multiple' => [
                    'type' => 'bool',
                    'default' => false,
                ],
                'sanitizer' => [
                    'type' => 'callable',
                    'default' => [$this, 'sanitizer'],
                ],
            ]
        );
    }

    public function getTwigArgs($value): array
    {
        $this->validateData();

        $options = [];

        if ($this->get('placeholder')) {
            $options = [
                [
                    'label' => $this->get('placeholder'),
                    'value' => '',
                    'selected' => '',
                ]
            ];
        }

        foreach ($this->get('options') as $val => $label) {
            $selected = $this->get('multiple') ? in_array($val, (array)$value) : $val == $value;

            $options[] = [
                'label' => $label,
                'value' => $val,
                'selected' => $selected ? 'selected' : '',
            ];
        }

        $this->set('options', $options);

        return $this->data;
    }

    public function sanitizer($value)
    {
        return is_array($value) ? array_map('sanitize_text_field', $value) : sanitize_text_field($value);
    }
}
