<?php

namespace AlexDashkin\Adwpfw\Customizer;

/**
 * Customizer Control Category Order
 */
class CatOrderControl extends \WP_Customize_Control
{

    private $app;

    private $categories;

    public $type = 'category_order';

    public function __construct($manager, $id, $args, $main)
    {
        parent::__construct($manager, $id, $args);
        $this->app = $main;
        $this->categories = $args['categories'];
    }

    public function render_content()
    {

        $values = [];

        if (($value = $this->value()) && $valueArr = json_decode($value, true)) {
            foreach ($valueArr as $item) {
                $values[$item['id']] = $item;
            }
        }

        $args = [
            'categories' => $this->categories,
            'data_attr' => $this->get_link(),
            'value' => $value,
            'values' => $values,
        ];

        echo $this->app->m('Common\Utils')->renderTwig('adwpfw/controls/cat-order', $args);
    }

//	public function content_template() {}
}
