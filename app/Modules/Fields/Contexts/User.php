<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

class User extends Context
{
    public function get(int $objectId = 0)
    {
        return $this->main->getUserMeta($this->fieldName, $objectId);
    }

    public function set($value, int $objectId = 0)
    {
        return $this->main->updateUserMeta($this->fieldName, $value, $objectId);
    }
}
