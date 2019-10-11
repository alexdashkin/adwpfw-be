<?php

namespace AlexDashkin\Adwpfw\Items\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Fields\Field;
use AlexDashkin\Adwpfw\Modules\Basic\Helpers;

/**
 * Metabox
 */
class Metabox extends ItemWithItems
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $slug Defaults to sanitized $title.
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
    public function add(array $data, App $app)
    {
        $this->items[] = Field::getField($data, $app);
    }

    /**
     * Get a Metabox Value.
     *
     * @param int|null $postId Post ID (defaults to the current post).
     * @return mixed
     */
    public function get($postId = null)
    {
        if (!$postId = get_post($postId)) {
            return '';
        }

        return get_post_meta($postId->ID, '_' . $this->prefix . '_' . $this->data['slug'], true);
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

        return update_post_meta($postId->ID, '_' . $this->prefix . '_' . $this->data['slug'], $value);
    }

    /**
     * Register the Metabox.
     */
    public function register()
    {
        $data = $this->data;

        add_meta_box(
            $data['slug'],
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

        foreach ($this->items as $field) {
            $fields[] = $field->getArgs($values);
        }

        echo $this->m('Twig')->renderFile('metabox', ['fields' => $fields]);
    }

    /**
     * Save the posted data
     *
     * @param array $data Posted Data
     * @param int $postId
     * @return array Success array to pass as Ajax response.
     */
    public function save($data, $postId)
    {
        $values = $this->get($postId);

        foreach ($this->items as $field) {

            if (empty($field->data['id']) || !array_key_exists($field->data['id'], $data)) {
                continue;
            }

            $fieldId = $field->data['id'];

            $values[$fieldId] = $field->sanitize($data[$fieldId]);
        }

        $this->set($values, $postId);

        do_action('adwpfw_metabox_saved', $this, $values); // todo add more hooks

        return Helpers::returnSuccess();
    }
}
