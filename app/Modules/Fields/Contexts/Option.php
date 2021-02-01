<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

class Option extends Context
{
    /**
     * Get Value
     *
     * @return mixed
     */
    public function get()
    {
        return $this->main->getOption($this->fieldName);
    }

    /**
     * Set Value
     *
     * @param mixed $value
     * @return bool
     */
    public function set($value): bool
    {
        return $this->main->updateOption($this->fieldName, $value);
    }
}
