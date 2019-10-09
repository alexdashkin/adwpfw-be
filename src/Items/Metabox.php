<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Modules\Helpers;

/**
 * Metabox
 */
class Metabox extends Item
{
    /**
     * @var FormField[]
     */
    private $fields;

    /**
     * Constructor
     *
     * @param array $data {
     * @type string $title Metabox title. Required.
     * @type string $slug Defaults to prefixed sanitized Title
     * @type array $screen For which Post Types to show
     * @type string $context normal/side/advanced. Default 'normal'.
     * @type string $priority high/low/default. Default 'default'.
     * @type array $fields
     * }
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'title' => [
                'required' => true,
            ],
            'slug' => [
                'default' => $this->getDefaultSlug($data['title']),
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
            $this->fields[] = FormField::getField($field, $app);
        }
    }

    /**
     * Get a Metabox Value
     *
     * @param int|null $postId Post ID (defaults to the current post)
     * @return mixed
     */
    public function get($postId = null)
    {
        if (!$postId = get_post($postId)) {
            return '';
        }

        return get_post_meta($postId->ID, '_' . $this->config['prefix'] . '_' . $this->data['slug'], true);
    }

    /**
     * Set a Metabox Value
     *
     * @param mixed $value Value to set
     * @param int|null $postId Post ID (defaults to the current post)
     * @return bool
     */
    public function set($value, $postId = null)
    {
        if (!$postId = get_post($postId)) {
            return '';
        }

        return update_post_meta($postId->ID, '_' . $this->config['prefix'] . '_' . $this->data['slug'], $value);
    }

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

    public function render($post)
    {
        $values = $this->get($post->ID);

        foreach ($this->fields as $field) {
            $fields[] = $field->getArgs($values);
        }

        echo $this->m('Utils')->renderFile('metabox', ['fields' => $fields]);
    }

    public function save($data, $postId)
    {
        $values = $this->get($postId);

        foreach ($this->fields as $field) {

            if (empty($field->data['id']) || !array_key_exists($field->data['id'], $data)) {
                continue;
            }

            $fieldId = $field->data['id'];

            $value = Helpers::trim($data[$fieldId]); // todo validation

            $values[$fieldId] = $value;
        }

        $this->set($values, $postId);

        do_action('adwpfw_metabox_saved', $this, $values); // todo add more hooks

        return $this->m('Utils')->returnSuccess();
    }
}
