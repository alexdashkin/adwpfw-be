<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Fields\Field;

/**
 * Post Metabox.
 */
class Metabox extends ItemWithItems
{
    /**
     * @var Field[]
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Metabox title. Required.
     * @type array $screen For which Post Types to show. Default ['post', 'page'].
     * @type string $context normal/side/advanced. Default 'normal'.
     * @type string $priority high/low/default. Default 'default'.
     * @type array $fields Metabox fields. Default [].
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
            'screen' => [
                'type' => 'array',
                'default' => ['post', 'page'],
            ],
            'context' => [
                'default' => 'normal',
            ],
            'priority' => [
                'default' => 'default',
            ],
            'fields' => [
                'type' => 'array',
                'default' => [],
                'def' => [
                    'id' => 'field',
                    'type' => 'text',
                ],
            ],
        ];

        parent::__construct($app, $data, $props);

        foreach ($this->data['fields'] as $field) {
            $field['layout'] = 'metabox-field';
            $field['form'] = $this->data['id'];
            $this->add($field);
        }
    }

    /**
     * Add Field.
     *
     * @param array $data Data passed to the Field Constructor.
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = Field::getField($this->app, $data);
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
            return false;
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

        $args = [
            'fields' => $fields,
            'context' => $this->data['context'],
        ];

        echo $this->m('Twig')->renderFile('templates/metabox', $args);
    }

    /**
     * Save the posted data.
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
