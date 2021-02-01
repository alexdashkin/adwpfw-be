<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

class Post extends Context
{
    /**
     * Get Value
     *
     * @param int $objectId
     * @return mixed
     */
    public function get(int $objectId = 0)
    {
        return $this->main->getPostMeta($this->fieldName, $objectId);
    }

    /**
     * Set Value
     *
     * @param mixed $value
     * @param int $objectId
     * @return bool
     */
    public function set($value, int $objectId = 0)
    {
        return $this->main->updatePostMeta($this->fieldName, $value, $objectId);
    }
}
