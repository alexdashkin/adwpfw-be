<?php

namespace AlexDashkin\Adwpfw\Modules\Fields;

use AlexDashkin\Adwpfw\Modules\Module;

/**
 * name*, id, tpl, label, placeholder, desc, required, default, classes
 */
class Field extends Module
{
    /**
     * Template Args
     *
     * @var array
     */
    protected $args = [];

    /**
     * Set Field value
     *
     * @param mixed $value
     * @param int $objectId
     */
    public function setValue($value, int $objectId = 0)
    {
        $contexts = [
            'option' => 'updateOption',
            'post' => 'updatePostMeta',
            'term' => 'updateTermMeta',
            'user' => 'updateUserMeta',
        ];

        $name = $this->getProp('name');
        $context = $this->getProp('context');

        $value = $this->sanitize($value);

        if (array_key_exists($context, $contexts)) {
            $method = $contexts[$context];
            $this->main->$method($name, $value, $objectId);
        }
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
        $name = $this->getProp('name');
        $context = $this->getProp('context');

        if ('widget' === $context) {
            return $this->getProp('value');
        }

        $contexts = [
            'option' => 'getOption',
            'post' => 'getPostMeta',
            'term' => 'getTermMeta',
            'user' => 'getUserMeta',
        ];

        $method = $contexts[$context];

        return array_key_exists($context, $contexts) ? $this->main->$method($name, $objectId) : null;
    }

    /**
     * Render Admin Field
     *
     * @param int $objectId
     * @return string
     */
    public function render(int $objectId): string
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
                'name' => sprintf('%s[%s]', $prefix, $this->getProp('name')),
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
