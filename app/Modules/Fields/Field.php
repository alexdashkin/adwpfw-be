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
     * Render Admin Field
     *
     * @param mixed $value
     * @return string
     */
    public function render($value): string
    {
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
     * Sanitize field value on save
     *
     * @param mixed $value
     * @return mixed
     */
    public function sanitize($value)
    {
        return apply_filters(sprintf('%s_sanitize_field_%s', $this->prefix, $this->getProp('type')), $value, $this);
    }

    /**
     * Render a set of fields
     *
     * @param array $fields
     * @param array $values
     * @return array
     */
    public static function getArgsForMany(array $fields, array $values): array
    {
        $args = [];

        foreach ($fields as $field) {
            $fieldArgs = $field->getProps();
            $fieldArgs['content'] = $field->render($values[$fieldArgs['name']] ?? null);
            $args[] = $fieldArgs;
        }

        return $args;
    }

    /**
     * Get several Field values helper
     *
     * @param Field[] $fields
     * @param array $form
     * @return array
     */
    public static function getFieldValues(array $fields, array $form): array
    {
        $values = [];

        foreach ($fields as $field) {
            $fieldName = $field->getProp('name');

            if (empty($fieldName) || !array_key_exists($fieldName, $form)) {
                continue;
            }

            $values[$fieldName] = $field->sanitize($form[$fieldName]);
        }

        return $values;
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
                'name' => sprintf('%s[%s][%s]', $prefix, $this->getProp('form'), $this->getProp('name')),
                'required' => $this->getProp('required') ? 'required' : '',
            ]
        );

        $this->args = array_merge($this->args, $args);
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
            'classes' => 'widefat',
        ];
    }
}
