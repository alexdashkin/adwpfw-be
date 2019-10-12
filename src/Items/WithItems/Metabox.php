<?php

namespace AlexDashkin\Adwpfw\Items\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Fields\Field;

/**
 * Metabox
 */
class Metabox extends ItemWithItems
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Metabox title. Required.
     * @type array $screen For which Post Types to show.
     * @type string $context normal/side/advanced. Default 'normal'.
     * @type string $priority high/low/default. Default 'default'.
     * @type array $fields Metabox fields.
     * }
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['title']),
            ],
            'title' => [
                'required' => true,
            ],
            'screen' => [
                'type' => 'array',
                'default' => [],
            ],
            'context' => [
                'default' => 'normal',
            ],
            'priority' => [
                'default' => 'default',
            ],
            'fields' => [
                'type' => 'array',
                'def' => [
                    'id' => 'field',
                    'type' => 'text',
                    'name' => 'Field',
                ],
            ],
        ];

        parent::__construct($data, $app, $props);

        foreach ($this->data['fields'] as $field) {
            $field['layout'] = 'metabox-field';
            $field['form'] = $this->data['id'];
            $this->add($field, $app);
        }
    }

    /**
     * Add Field
     *
     * @param array $data Data passed to the Field Constructor.
     * @param App $app
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = Field::getField($data);
    }

    /**
     * Get a Metabox Value.
     *
     * @param int|null $postId Post ID (defaults to the current post).
     * @return mixed
     */
    public function get($postId = null)
    {
        if (!$post = get_post($postId)) {
            return '';
        }

        return get_post_meta($post->ID, '_' . $this->prefix . '_' . $this->data['id'], true) ?: [];
    }

    /**
     * Set a Metabox Value.
     *
     * @param mixed $value Value to set.
     * @param int|null $postId Post ID (defaults to the current post).
     * @return bool
     */
    public function set($value, $postId = null)
    {
        if (!$postId = get_post($postId)) {
            return '';
        }

        return update_post_meta($postId->ID, '_' . $this->prefix . '_' . $this->data['id'], $value);
    }

    /**
     * Register the Metabox.
     */
    public function register()
    {
        $data = $this->data;

        $id = $this->prefix . '_' . $data['id'];

        add_meta_box(
            $id,
            $data['title'],
            [$this, 'render'],
            $data['screen'],
            $data['context'],
            $data['priority']
        );
    }

    /**
     * Render the Metabox
     *
     * @param \WP_Post $post
     */
    public function render($post)
    {
        $values = $this->get($post->ID);

        $fields = [];

        foreach ($this->items as $field) {
            $fields[] = $field->getArgs($values);
        }

        echo $this->m('Twig')->renderFile('templates/metabox', ['fields' => $fields]);
    }

    /**
     * Save the posted data
     *
     * @param array $data Posted Data
     * @param int $postId
     */
    public function save($data, $postId)
    {
        if (empty($data[$this->data['id']])) {
            return;
        }

        $form = $data[$this->data['id']];

        $values = $this->get($postId);

        foreach ($this->items as $field) {

            if (empty($field->data['id']) || !array_key_exists($field->data['id'], $form)) {
                continue;
            }

            $fieldId = $field->data['id'];

            $values[$fieldId] = $field->sanitize($form[$fieldId]);
        }

        $this->set($values, $postId);

        do_action('adwpfw_metabox_saved', $this, $values); // todo add more hooks
    }
}
