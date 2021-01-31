<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

class Post extends Context
{
    public function get(int $objectId = 0)
    {
        return $this->main->getPostMeta($this->fieldName, $objectId);
    }

    public function set($value, int $objectId = 0)
    {
        return $this->main->updatePostMeta($this->fieldName, $value, $objectId);
    }
}
