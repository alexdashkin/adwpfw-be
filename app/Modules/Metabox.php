<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\_Fields\Field;

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
        $this->addHook('add_meta_boxes', [$this, 'register'], 20);
        $this->addHook('save_post', [$this, 'save']);
    }

    /**
     * Register the Page
     */
    public function register()
    {
        $id = $this->config('prefix') . '_' . $this->getProp('id');

        add_meta_box(
            $id,
            $this->getProp('title'),
            [$this, 'render'],
            $this->getProp('screen'),
            $this->getProp('context'),
            $this->getProp('priority')
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
            $fieldName = $field->gp('name');

            $twigArgs = $field->getTwigArgs($values[$fieldName] ?? null);

            $fields[] = $twigArgs;
        }

        $args = [
            'fields' => $fields,
            'context' => $this->getProp('context'),
        ];

        echo $this->twig('templates/metabox', $args);
    }

    /**
     * Get a Metabox Value.
     *
     * @param int $postId Post ID (defaults to the current post).
     * @return mixed
     */
    public function getValue(int $postId = 0)
    {
        if (!$post = get_post($postId)) {
            return '';
        }

        return get_post_meta($post->ID, '_' . $this->config('prefix') . '_' . $this->getProp('id'), true) ?: [];
    }

    /**
     * Set a Metabox Value.
     *
     * @param mixed $value Value to set.
     * @param int $postId Post ID (defaults to the current post).
     * @return bool
     */
    public function setValue($value, int $postId = 0): bool
    {
        if (!$postId = get_post($postId)) {
            return false;
        }

        return update_post_meta($postId->ID, '_' . $this->config('prefix') . '_' . $this->getProp('id'), $value);
    }

    /**
     * Save the posted data
     *
     * @param int $postId
     */
    public function save(int $postId)
    {
        if (empty($_POST[$this->config('prefix')][$this->getProp('id')])) {
            return;
        }

        $form = $_POST[$this->config('prefix')][$this->getProp('id')];

        $values = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->gp('name');

            if (empty($fieldName) || !array_key_exists($fieldName, $form)) {
                continue;
            }

            $values[$fieldName] = $field->sanitize($form[$fieldName]);
        }

        $this->setValue($values, $postId);

        do_action('adwpfw_metabox_saved', $this, $postId, $values);
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
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
