<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * name*, id, tpl, label, placeholder, desc, required, default, classes, filter, sanitizer
 */
class Field extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $prefix = $this->config('prefix');

        // Common rendering filters
        $this->addHook(sprintf('%s_render_field_select', $prefix), [$this, 'select']);
        $this->addHook(sprintf('%s_render_field_select2', $prefix), [$this, 'select2']);

        // Common saving sanitizers
        $this->addHook(sprintf('%s_sanitize_field_text', $prefix), 'sanitize_text_field');
    }

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

        $prefix = $this->config('prefix');

        // Prepare template args
        $args = $this->getProps();
        $args['prefix'] = $prefix;
        $args['id'] = $prefix . '-' . $this->getProp('id');
        $args['name'] = sprintf('%s[%s][%s]', $prefix, $this->getProp('form'), $this->getProp('name'));
        $args['required'] = $this->getProp('required') ? 'required' : '';
        $args['value'] = $value;

        $args = apply_filters(sprintf('%s_render_field_%s', $prefix, $this->getProp('type')), $args);

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
        return apply_filters(sprintf('%s_sanitize_field_%s', $this->config('prefix'), $this->getProp('type')), $value);
    }

    /**
     * Filter Select Template Args
     *
     * @param array $args
     * @return array
     */
    public function select(array $args): array
    {
        $value = $args['value'];
        $multiple = !empty($args['multiple']);
        $args['multiple'] = $multiple ? 'multiple' : '';

        if ($multiple) {
            $args['name'] .= '[]';
        }

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

        foreach ($args['options'] as $val => $label) {
            $selected = $multiple ? in_array($val, (array)$value) : $val == $value;

            $options[] = [
                'label' => $label,
                'value' => $val,
                'selected' => $selected ? 'selected' : '',
            ];
        }

        $args['options'] = $options;

        return $args;
    }

    /**
     * Filter Select2 Template Args
     *
     * @param array $args
     * @return array
     */
    public function select2(array $args): array
    {
        $args = $this->select($args);

        $value = $args['value'];
        $multiple = !empty($args['multiple']);

        $valueArr = $multiple ? (array)$value : [$value];

        foreach ($valueArr as $item) {
            if (!$this->app->main->arraySearch($args['options'], ['value' => $item])) {
                $args['options'][] = [
                    'label' => !empty($this->getProp('label_cb')) ? $this->getProp('label_cb')($item) : $item,
                    'value' => $item,
                    'selected' => 'selected',
                ];
            }
        }

        return $args;
    }

    /**
     * Render a set of fields
     *
     * @param array $fields
     * @param array $values
     * @return string
     */
    public static function renderMany(array $fields, array $values): string
    {
        $html = '';

        foreach ($fields as $field) {
            $fieldName = $field->getProp('name');

            $html .= $field->render($values[$fieldName] ?? null);
        }

        return $html;
    }

    /**
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        $type = $this->getProp('type');

        switch ($key) {
            case 'id':
                return sanitize_key(str_replace([' ', '_'], '-', $this->getProp('name')));

            case 'tpl':
                return $type;
        }

        return null;
    }
}
