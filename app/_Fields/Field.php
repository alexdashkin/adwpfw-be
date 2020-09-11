<?php

namespace AlexDashkin\Adwpfw\_Fields;

use AlexDashkin\Adwpfw\Modules\Module;

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
        // Validate props
        $this->validateData();

        // Set default value if not set
        if (is_null($value) && !is_null($this->getProp('default'))) {
            $value = $this->getProp('default');
        }

        // Call filter if set
        $value = is_callable($this->getProp('filter')) ? $this->getProp('filter')($value) : $value;

        // Set value prop
        $this->setProp('value', $value);

        // Return all data
        return $this->getProps();
    }

    /**
     * Sanitize field value
     *
     * @param mixed $value
     * @return mixed
     */
    public function sanitize($value)
    {
        return is_callable($this->getProp('sanitizer')) ? $this->getProp('sanitizer')($value) : $value;
    }

    /**
     * Get Field props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
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
                'type' => 'mixed',
                'default' => null,
            ],
            'sanitizer' => [
                'type' => 'callable',
                'default' => null,
            ],
            'filter' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];
    }
}
