<?php

namespace AlexDashkin\Adwpfw\Entities;

/**
 * Module Item Basic Class
 */
abstract class Entity
{
    /**
     * @var array Entity Data
     */
    protected $data = [];

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
        $data = array_merge($this->defaults, $data);

        $data['id'] = $data['id'] ?: sanitize_title($data['title']);

        $this->data = $data;
    }


}
