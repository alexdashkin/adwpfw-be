<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

class Option extends Context
{
    /**
     * Get Value
     *
     * @return mixed
     */
    public function get(int $objectId)
    {
        return $this->main->getOption($this->fieldName);
    }

    /**
     * Set Value
     *
     * @param mixed $value
     * @return bool
     */
    public function set($value, int $objectId): bool
    {
        return $this->main->updateOption($this->fieldName, $value);
    }
}
