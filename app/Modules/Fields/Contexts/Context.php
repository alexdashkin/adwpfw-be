<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

use AlexDashkin\Adwpfw\Core\Main;
use AlexDashkin\Adwpfw\Modules\Fields\Field;

abstract class Context
{
    protected $field;
    protected $fieldName;
    protected $main;

    public function __construct(Field $field, Main $main)
    {
        $this->field = $field;
        $this->fieldName = $field->getProp('name');
        $this->main = $main;
    }

    public function getFieldName() {
        return sprintf('%s[%s]', $this->main->getPrefix(), $this->fieldName);
    }

    abstract public function get(int $objectId = 0);

    abstract public function set($value, int $objectId = 0);
}
