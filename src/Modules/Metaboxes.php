<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Metabox;

/**
 * Posts Metaboxes
 */
class Metaboxes extends ItemsModule
{
    private $remove = [];

    /**
     * Constructor
     *
     * @param App $app
     */
    protected function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add an Item
     *
     * @param array $data
     * @param App $app
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new Metabox($data, $app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
        add_action('add_meta_boxes', [$this, 'register'], 20, 0);
        add_action('save_post', [$this, 'save']);
    }

    /**
     * Remove Metaboxes
     *
     * @param array $metaboxes {
     * @type string $id
     * @type array $screen
     * @type string $context
     * }
     */
    public function remove(array $metaboxes)
    {
        foreach ($metaboxes as &$metabox) {
            $metabox = array_merge([
                'screen' => [],
            ], $metabox);
        }

        $this->remove = array_merge($this->remove, $metaboxes);
    }

    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }

        foreach ($this->remove as $item) {
            foreach (['normal', 'side', 'advanced'] as $context) {
                remove_meta_box($item['id'], $item['screen'], $context);
            }
        }
    }

    public function save($postId) // todo add hooks
    {
        if (empty($_POST[$this->config['prefix']])) { // todo will not work with multiple MB on one page
            return $this->m('Utils')->returnError('Form data is empty');
        }

        $form = $_POST[$this->config['prefix']];

        foreach ($this->items as $item) {
            if ($item->data['slug'] === $form['slug']) {
                return $item->save($form, $postId);
            }
        }

        return $this->m('Utils')->returnError('Metabox not found');
    }
}
