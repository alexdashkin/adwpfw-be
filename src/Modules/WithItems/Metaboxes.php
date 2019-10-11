<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\WithItems\Metabox;

/**
 * Posts Metaboxes
 */
class Metaboxes extends ModuleWithItems
{
    /**
     * @var array Registered Metaboxes to remove.
     */
    private $remove = [];

    /**
     * Constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add Metabox.
     *
     * @param array $data
     *
     * @see Metabox::__construct();
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new Metabox($data, $this->app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function init()
    {
        add_action('add_meta_boxes', [$this, 'register'], 20, 0);
        add_action('save_post', [$this, 'save']);
    }

    /**
     * Mark registered Metaboxes to be removed
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

    /**
     * Register and Remove Metaboxes
     */
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

    public function get($id, $post = null)
    {
        if ($item = $this->searchItems(['id' => $id])) {
            return $item->get($post);
        }

        return null;
    }

    public function set($id, $value, $post = null)
    {
        if ($item = $this->searchItems(['id' => $id])) {
            return $item->set($value, $post);
        }

        return null;
    }

    /**
     * Save posted data.
     *
     * @param int $postId
     */
    public function save($postId) // todo add hooks
    {
        if (empty($_POST[$this->config['prefix']])) { // todo will not work with multiple MB on one page
            return;
        }

        $form = $_POST[$this->config['prefix']];

        foreach ($this->items as $item) {
            if ($item->data['slug'] === $form['slug']) {
                $item->save($form, $postId);
            }
        }
    }
}
