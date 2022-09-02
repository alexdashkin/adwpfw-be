<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\{App, Exceptions\AppException, Modules\FieldHolder, Modules\Module};

/**
 * Form Field
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
     * @var int
     */
    protected $objectId = 0;

    /**
     * @var FieldHolder
     */
    protected $parent;

    /**
     * Get Field object by type
     *
     * @param array $args
     * @param App $app
     * @return Field
     * @throws AppException
     */
    public static function getField(array $args, App $app)
    {
        if (empty($args['type'])) {
            throw new AppException('Type is required for fields');
        }

        switch ($args['type']) {
            case 'checkbox':
                return new Checkbox($args, $app);
            case 'number':
                return new Number($args, $app);
            case 'select':
                return new Select($args, $app);
            case 'select2':
                return new Select2($args, $app);
            case 'text':
                return new Text($args, $app);
            case 'textarea':
                return new Textarea($args, $app);
            default:
                return new Field($args, $app);
        }
    }

    /**
     * Set Parent Page
     *
     * @param FieldHolder $parent
     */
    public function setParent(FieldHolder $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Set Field value
     *
     * @param mixed $value
     * @param int $objectId
     */
    public function setValue($value, int $objectId = 0)
    {
        $this->parent->setFieldValue($this, $this->sanitize($value), $objectId);
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
        return $this->parent->getFieldValue($this, $objectId);
    }

    /**
     * Render Field
     *
     * @param int $objectId
     * @return string
     */
    public function render(int $objectId = 0): string
    {
        // Prepare template args
        $this->prepareArgs($objectId);

        // Render template
        return $this->app->render('fields/' . $this->getProp('template'), $this->args);
    }

    /**
     * Prepare Template Args
     *
     * @param int $objectId
     */
    protected function prepareArgs(int $objectId = 0)
    {
        $args = [
            'name' => $this->parent->getFieldName($this),
            'value' => $this->parent->getFieldValue($this, $objectId),
            'required' => $this->getProp('required') ? 'required' : '',
        ];

        $this->args = array_merge($this->args, $this->getProps(), $args);
    }

    /**
     * Sanitize field value on save
     *
     * @param mixed $value
     * @return mixed
     */
    protected function sanitize($value)
    {
        return $value;
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        return [
            'id' => [
                'type' => 'string',
                'default' => function () {
                    return $this->prefixIt($this->getProp('name'));
                },
            ],
            'name' => [
                'type' => 'string',
                'default' => '',
            ],
            'type' => [
                'type' => 'string',
                'required' => true,
            ],
            'label' => [
                'type' => 'string',
                'default' => '',
            ],
            'placeholder' => [
                'type' => 'string',
                'default' => '',
            ],
            'description' => [
                'type' => 'string',
                'default' => '',
            ],
            'fieldClasses' => [
                'type' => 'string',
                'default' => '',
            ],
            'controlClasses' => [
                'type' => 'string',
                'default' => '',
            ],
            'template' => [
                'type' => 'string',
                'default' => function () {
                    return $this->getProp('type');
                },
            ],
        ];
    }
}
