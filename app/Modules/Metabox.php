<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Modules\Fields\Contexts\Post;
use AlexDashkin\Adwpfw\Modules\Fields\Field;

/**
 * title*, id, screen, context, priority
 */
class Metabox extends Module
{
    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * Add Field
     *
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $field->setProp('context', new Post($field, $this->main));

        $this->fields[] = $field;
    }

    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('add_meta_boxes', [$this, 'register'], 20);
        $this->addHook('save_post', [$this, 'save']);
    }

    /**
     * Register the Page
     */
    public function register()
    {
        $id = $this->prefix . '_' . $this->getProp('id');

        add_meta_box(
            $id,
            $this->getProp('title'),
            [$this, 'render'],
            $this->getProp('screen'),
            $this->getProp('context'),
            $this->getProp('priority')
        );


        // Enqueue assets
        foreach ($this->getProp('assets') as $index => $asset) {
            // Type here is CSS/JS
            $type = $asset['type'] ?? 'css';

            // Type for particular asset is admin/front
            $asset['type'] = 'admin';

            $args = [
                'id' => sprintf('%s-%d', $this->getProp('id'), $index),
                'type' => 'admin',
                'callback' => function () {
                    return in_array(get_current_screen()->id, $this->getProp('screen'));
                },
            ];

            $this->m('asset.' . $type, array_merge($args, $asset));
        }
    }

    /**
     * Render the Metabox
     *
     * @param \WP_Post $post
     */
    public function render(\WP_Post $post)
    {
        $args = $this->getProps();

        $args['fields'] = Field::renderMany($this->fields, $post->ID);

        echo $this->main->render('templates/metabox', $args);
    }

    /**
     * Save the posted data
     *
     * @param int $postId
     */
    public function save(int $postId)
    {
        if (empty($_POST[$this->prefix])) {
            return;
        }

        $values = $_POST[$this->prefix];

        Field::setMany($this->fields, $values, $postId);

        do_action('adwpfw_metabox_saved', $this, $postId, $values);
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'title' => 'Metabox',
            'id' => function () {
                return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
            },
            'screen' => ['post', 'page'],
            'context' => 'normal',
            'assets' => [],
        ];
    }
}
