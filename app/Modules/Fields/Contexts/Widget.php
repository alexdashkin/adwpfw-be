<?php

namespace AlexDashkin\Adwpfw\Modules\Fields\Contexts;

class Widget extends Context
{
    /** @var \WP_Widget */
    private $widget;

    /**
     * Set WP_Widget
     *
     * @param \WP_Widget $widget
     */
    public function setWidget(\WP_Widget $widget)
    {
        $this->widget = $widget;
    }

    /**
     * Get field "name" attr for template
     *
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->widget->get_field_name($this->fieldName);
    }
}
