<?php

namespace AlexDashkin\Adwpfw\Modules\Fields;

use AlexDashkin\Adwpfw\Exceptions\AppException;
use AlexDashkin\Adwpfw\Modules\Fields\Contexts\Context;
use AlexDashkin\Adwpfw\Modules\Module;

/**
 * name*, id, tpl, context, label, placeholder, desc, required, default, classes
 */
class Field extends Module
{
    /**
     * Template Args
     *
     * @var array
     */
    protected $args = [];

    protected $context;

    public function getContext(): Context
    {
        if ($this->context) {
            return $this->context;
        }

        $context = $this->getProp('context');

        if (!$context instanceof Context) {
            throw new AppException('Invalid Field Context');
        }

        $this->context = $context;

        return $context;
    }

    /**
     * Set Field value
     *
     * @param mixed $value
     */
    public function setValue($value, int $objectId = 0)
    {
        $this->getContext()->set($this->sanitize($value), $objectId);
    }

    /**
     * Set values for a set of fields
     *
     * @param Field[] $fields
     * @param array $values
     * @param int $objectId
     */
    public static function setMany(array $fields, array $values, int $objectId = 0)
    {
        foreach ($fields as $field) {
            $name = $field->getProp('name');
            if (array_key_exists($name, $values)) {
                $field->setValue($values[$name], $objectId);
            }
        }
    }

    /**
     * Get Field value
     *
     * @param int $objectId
     * @return mixed
     */
    public function getValue(int $objectId = 0)
    {
        return $this->getContext()->get($objectId);
    }

    /**
     * Render Admin Field
     *
     * @param int $objectId
     * @return string
     */
    public function render(int $objectId = 0): string
    {
        // Get Value
        $value = $this->getValue($objectId);

        // Set default value if not set
        $this->args['value'] = (is_null($value) && !is_null($this->getProp('default'))) ? $this->getProp('default') : $value;

        // Prepare template args
        $this->prepareArgs();

        // Filter args before passing to template
        $args = apply_filters(sprintf('%s_render_field_%s', $this->prefix, $this->getProp('type')), $this->args, $this);

        // Render template
        return $this->main->render('fields/' . $this->getProp('tpl'), $args);
    }

    /**
     * Render a set of fields
     *
     * @param Field[] $fields
     * @param int $objectId
     * @return array
     */
    public static function renderMany(array $fields, int $objectId = 0): array
    {
        $args = [];

        foreach ($fields as $field) {
            $fieldArgs = $field->getProps();
            $fieldArgs['content'] = $field->render($objectId);
            $args[] = $fieldArgs;
        }

        return $args;
    }

    /**
     * Prepare Template Args
     */
    protected function prepareArgs()
    {
        $prefix = $this->prefix;

        $args = array_merge(
            $this->getProps(),
            [
                'prefix' => $prefix,
                'name' => $this->getContext()->getFieldName(),
                'required' => $this->getProp('required') ? 'required' : '',
            ]
        );

        $this->args = array_merge($this->args, $args);
    }

    /**
     * Sanitize field value on save
     *
     * @param mixed $value
     * @return mixed
     */
    protected function sanitize($value)
    {
        return apply_filters(sprintf('%s_sanitize_field_%s', $this->prefix, $this->getProp('type')), $value, $this);
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'name' => 'field', // some fields have no name (e.g. heading)
            'id' => function () {
                return $this->prefix . '-' . sanitize_key(str_replace([' ', '_'], '-', $this->getProp('name')));
            },
            'tpl' => $this->getProp('type'),
            'label' => '',
            'placeholder' => '',
            'desc' => '',
            'fieldClasses' => '',
            'controlClasses' => '',
        ];
    }
}
