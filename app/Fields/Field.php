<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\Abstracts\Module;

/**
 * Form Field
 */
abstract class Field extends Module
{
    /**
     * Get args for Twig template
     *
     * @param mixed $value
     * @return array
     */
    public function getTwigArgs($value): array
    {
        $this->validateData();

        $this->set('value', $value);

        return $this->data;
    }

    /**
     * Sanitize field value
     *
     * @param mixed $value
     * @return mixed
     */
    public function sanitize($value)
    {
        return is_callable($this->get('sanitizer')) ? $this->get('sanitizer')($value) : $value;
    }

    /**
     * Get Field props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'prefix' => [
                'required' => true,
            ],
            'layout' => [
                'required' => true,
            ],
            'form' => [
                'required' => true,
            ],
            'name' => [
                'required' => true,
            ],
            'tpl' => [
                'required' => true,
            ],
            'placeholder' => [
                'default' => '',
            ],
            'class' => [
                'default' => 'widefat',
            ],
            'required' => [
                'type' => 'bool',
                'default' => false,
            ],
            'label' => [
                'default' => '',
            ],
            'desc' => [
                'default' => '',
            ],
            'default' => [
                'default' => null,
            ],
            'sanitizer' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];
    }
}
