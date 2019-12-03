<?php

namespace AlexDashkin\Adwpfw\Items\Customizer;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\ItemWithItems;

/**
 * Customizer Panel.
 */
class Panel extends ItemWithItems
{
    /**
     * @var Section[]
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Default sanitized title.
     * @type string $title Required.
     * @type string $description Default empty.
     * @type int $priority Default 160.
     * @type array $sections
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['title']),
            ],
            'title' => [
                'required' => true,
            ],
            'description' => [
                'default' => '',
            ],
            'priority' => [
                'type' => 'int',
                'default' => 160,
            ],
            'sections' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        parent::__construct($app, $data, $props);

        foreach ($this->data['sections'] as $section) {
            $this->add($section);
        }
    }

    /**
     * Add Section.
     *
     * @param App $app
     * @param array $data Data passed to the Section Constructor.
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $data['panel'] = $this->prefix . '_' . $this->data['id'];

        $this->items[] = new Section($this->app, $data);
    }

    /**
     * Register Panel.
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_panel($this->prefix . '_' . $this->data['id'], $this->data);

        foreach ($this->items as $section) {
            $section->register($customizer);
        }
    }

    /**
     * Get default Panel ID.
     * Not working with uniqid() as on subsequent calls it's different
     * and the Panel disappears from Customizer on page load
     *
     * @param string $base
     * @return string
     */
    protected function getDefaultId($base)
    {
        return esc_attr(sanitize_key(str_replace(' ', '_', $base)));
    }
}
