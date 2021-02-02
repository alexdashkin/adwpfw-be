<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

use AlexDashkin\Adwpfw\Core\Main;
use AlexDashkin\Adwpfw\Modules\Fields\Field;

/**
 * Context for fields
 */
abstract class Context
{
    /** @var Field */
    protected $field;

    /** @var string */
    protected $fieldName;

    /** @var Main */
    protected $main;

    /**
     * Context constructor
     *
     * @param Field $field
     * @param Main $main
     */
    public function __construct(Field $field, Main $main)
    {
        $this->field = $field;
        $this->fieldName = $field->getProp('name');
        $this->main = $main;
    }

    /**
     * Get field "name" attr for template
     *
     * @return string
     */
    public function getFieldName(): string
    {
        return sprintf('%s[%s]', $this->main->getPrefix(), $this->fieldName);
    }

    abstract public function get(int $objectId);

    abstract public function set($value, int $objectId);
}
