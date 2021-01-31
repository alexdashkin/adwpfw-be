<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

class Widget extends Context
{
    /** @var \WP_Widget */
    private $widget;

    public function setWidget(\WP_Widget $widget)
    {
        $this->widget = $widget;
    }

    public function get(int $objectId = 0)
    {
        return $this->widget; // todo
    }

    public function set($value, int $objectId = 0)
    {
        return null;
    }

    public function getFieldName()
    {
        return $this->widget->get_field_name($this->fieldName);
    }
}
