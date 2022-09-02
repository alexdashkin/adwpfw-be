<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Fields\Field;

abstract class FieldHolder extends Module
{
    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * Add Field
     *
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $field->setParent($this);

        $this->fields[] = $field;
    }

    /**
     * Get field "name" attr for template
     *
     * @param Field $field
     * @return string
     */
    public function getFieldName(Field $field): string
    {
        return $this->prefixIt($field->getProp('name'));
    }

    /**
     * Get Fields Template Args
     *
     * @param int $objectId
     * @return array
     */
    protected function getFieldsArgs(int $objectId = 0): array
    {
        $args = [];

        foreach ($this->fields as $field) {
            $fieldArgs = $field->getProps();
            $fieldArgs['content'] = $field->render($objectId);
            $args[] = $fieldArgs;
        }

        return $args;
    }

    /**
     * Get field value
     *
     * @param Field $field
     * @param int $objectId
     * @return mixed
     */
    abstract public function getFieldValue(Field $field, int $objectId = 0);

    /**
     * Set field value
     *
     * @param Field $field
     * @param $value
     * @param int $objectId
     * @return bool
     */
    abstract public function setFieldValue(Field $field, $value, int $objectId = 0): bool;
}
