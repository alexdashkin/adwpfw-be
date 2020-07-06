<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\Fields\Field;

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
        $this->fields[] = $field;
    }

    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('add_meta_boxes', [$this, 'register'], 20);
        $this->hook('save_post', [$this, 'save']);
    }

    /**
     * Register the Page
     */
    public function register()
    {
        $id = $this->get('prefix') . '_' . $this->get('id');

        add_meta_box(
            $id,
            $this->get('title'),
            [$this, 'render'],
            $this->get('screen'),
            $this->get('context'),
            $this->get('priority')
        );
    }

    /**
     * Render the Metabox
     *
     * @param \WP_Post $post
     */
    public function render(\WP_Post $post)
    {
        $values = $this->getValue($post->ID);

        $fields = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->get('name');

            $twigArgs = $field->getTwigArgs($values[$fieldName] ?? null);

            $fields[] = $twigArgs;
        }

        $args = [
            'fields' => $fields,
            'context' => $this->get('context'),
        ];

        echo $this->twig('templates/metabox', $args);
    }

    /**
     * Get a Metabox Value.
     *
     * @param int $postId Post ID (defaults to the current post).
     * @return mixed
     */
    public function getValue($postId = 0)
    {
        if (!$post = get_post($postId)) {
            return '';
        }

        return get_post_meta($post->ID, '_' . $this->get('prefix') . '_' . $this->get('id'), true) ?: [];
    }

    /**
     * Set a Metabox Value.
     *
     * @param mixed $value Value to set.
     * @param int|null $postId Post ID (defaults to the current post).
     * @return bool
     */
    public function setValue($value, $postId = null)
    {
        if (!$postId = get_post($postId)) {
            return false;
        }

        return update_post_meta($postId->ID, '_' . $this->get('prefix') . '_' . $this->get('id'), $value);
    }

    /**
     * Save the posted data
     *
     * @param int $postId
     */
    public function save($postId)
    {
        if (empty($_POST[$this->get('prefix')][$this->get('id')])) {
            return;
        }

        $form = $_POST[$this->get('prefix')][$this->get('id')];

        $values = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->get('name');

            if (empty($fieldName) || !array_key_exists($fieldName, $form)) {
                continue;
            }

            $values[$fieldName] = $field->sanitize($form[$fieldName]);
        }

        $this->setValue($values, $postId);

        do_action('adwpfw_metabox_saved', $this, $values);
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'title' => [
                'required' => true,
            ],
            'id' => [
                'default' => function ($data) {
                    return sanitize_key(str_replace(' ', '-', $data['title']));
                },
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
        ];
    }
}
