<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

class Term extends Context
{
    public function get(int $objectId = 0)
    {
        return $this->main->getTermMeta($this->fieldName, $objectId);
    }

    public function set($value, int $objectId = 0)
    {
        return $this->main->updateTermMeta($this->fieldName, $value, $objectId);
    }
}
