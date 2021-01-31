<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

class Option extends Context
{
    public function get(int $objectId = 0)
    {
        return $this->main->getOption($this->fieldName);
    }

    public function set($value, int $objectId = 0)
    {
        return $this->main->updateOption($this->fieldName, $value);
    }
}
