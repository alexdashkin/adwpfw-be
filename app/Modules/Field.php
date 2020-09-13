<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * name*, id, tpl, label, placeholder, desc, required, default, classes
 */
class Field extends Module
{
    /**
     * Render Admin Field
     *
     * @param mixed $value
     * @return string
     */
    public function render($value): string
    {
        // Set default value if not set
        if (is_null($value) && !is_null($this->getProp('default'))) {
            $value = $this->getProp('default');
        }

        // Call filter if set
        $value = is_callable($this->getProp('filter')) ? $this->getProp('filter')($value) : $value;

        $prefix = $this->prefix;

        // Prepare template args
        $args = $this->getProps();
        $args['prefix'] = $prefix;
        $args['name'] = sprintf('%s[%s][%s]', $prefix, $this->getProp('form'), $this->getProp('name'));
        $args['required'] = $this->getProp('required') ? 'required' : '';
        $args['value'] = $value;

        $args = apply_filters(sprintf('%s_render_field_%s', $prefix, $this->getProp('type')), $args, $this);

        // Render template
        return $this->app->main->render('fields/' . $this->getProp('tpl'), $args);
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
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'name' => 'field',
            'id' => function () {
                return $this->prefix . '-' . sanitize_key(str_replace([' ', '_'], '-', $this->getProp('name')));
            },
            'tpl' => $this->getProp('type'),
            'label' => '',
            'placeholder' => '',
            'desc' => '',
            'classes' => 'widefat',
            'options' => [],
        ];
    }
}
