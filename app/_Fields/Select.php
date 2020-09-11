<?php

namespace AlexDashkin\Adwpfw\_Fields;

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
    protected function getInitialPropDefs(): array
    {
        return array_merge(
            parent::getInitialPropDefs(),
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

    /**
     * Get Args for Twig Template
     *
     * @param mixed $value
     * @return array
     */
    public function getTwigArgs($value): array
    {
        $this->validateData();

        $options = [];

        if ($this->getProp('placeholder') && !$this->getProp('multiple')) {
            $options = [
                [
                    'label' => $this->getProp('placeholder'),
                    'value' => '',
                    'selected' => '',
                ]
            ];
        }

        foreach ($this->getProp('options') as $val => $label) {
            $selected = $this->getProp('multiple') ? in_array($val, (array)$value) : $val == $value;

            $options[] = [
                'label' => $label,
                'value' => $val,
                'selected' => $selected ? 'selected' : '',
            ];
        }

        $this->setProp('options', $options);

        return $this->getProps();
    }

    /**
     * Sanitize Value
     *
     * @param mixed $value
     * @return array|string
     */
    public function sanitizer($value)
    {
        return is_array($value) ? array_map('sanitize_text_field', $value) : sanitize_text_field($value);
    }
}
