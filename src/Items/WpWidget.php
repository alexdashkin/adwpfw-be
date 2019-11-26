<?php

namespace AlexDashkin\Adwpfw\Items;

/**
 * WP Widget
 */
class Widget extends \WP_Widget
{
    private $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
        parent::__construct($data['id'], $data['name'], $data['options']);
    }

    public function widget($args, $instance)
    {
        echo $this->data['callback']();
    }
}
