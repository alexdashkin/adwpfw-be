<?php

namespace AlexDashkin\Adwpfw\Items;

/**
 * Module Item Basic Class
 */
abstract class Item
{
    /**
     * @var array Entity Data
     */
    public $data = [];

    /**
     * @var array Entity Defaults
     */
    protected $defaults = [];

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $data = $this->arrayMerge($this->defaults, $data);

        $data['id'] = $data['id'] ?: sanitize_title($data['title']);

        $this->data = $data;
    }

    /**
     * Helper for deep array merge
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    protected function arrayMerge(array $arr1, array $arr2)
    {
        foreach ($arr1 as $key => &$value) {
            if (!array_key_exists($key, $arr2)) {
                continue;
            }

            if (is_array($value) && is_array($arr2[$key])) {
                $value = $this->arrayMerge($value, $arr2[$key]);
            } else {
                $value = $arr2[$key];
            }
        }

        return $arr1;
    }
}
