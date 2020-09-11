<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Admin Form Field
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

        // Set value prop
        $this->setProp('value', $value);

        // Prepare template args
        $args = $this->getProps();
        $args['prefix'] = $this->config('prefix');

        // Render template
        return $this->app->main->render('fields/' . $this->getProp('tpl'), $args);
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
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        $type = $this->getProp('type');

        switch ($key) {
            case 'tpl':
                return $type;

            case 'filter':
                switch ($type) {
                    case 'select':
                        return [$this, 'select'];
                    case 'select2':
                        return [$this, 'select2'];
                }
        }

        return null;
    }

    /**
     * Get Select Template Args
     *
     * @param mixed $value
     * @return array
     */
    public function select($value): array
    {
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
     * Get Select2 Template Args
     *
     * @param mixed $value
     * @return array
     */
    public function select2($value): array
    {
        $args = $this->select($value);

        $multiple = $this->getProp('multiple');

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
}
