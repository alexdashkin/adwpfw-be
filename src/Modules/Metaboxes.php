<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Metabox;

/**
 * Posts Metaboxes.
 */
class Metaboxes extends ModuleWithItems
{
    /**
     * @var Metabox[]
     */
    protected $items = [];

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

        add_action('add_meta_boxes', [$this, 'register'], 20, 0);
        add_action('save_post', [$this, 'save']);
    }

    /**
     * Add Metabox.
     *
     * @param array $data
     *
     * @throws AdwpfwException
     * @see Metabox::__construct();
     *
     */
    public function add(array $data)
    {
        $this->items[] = new Metabox($this->app, $data);
    }

    /**
     * Mark registered Metaboxes to be removed.
     *
     * @param array $metaboxes {
     * @type string $id . Required.
     * @type array $screen . Default [].
     * @type string $context
     * }
     */
    public function remove(array $metaboxes)
    {
        foreach ($metaboxes as &$metabox) {
            $metabox = array_merge(
                [
                    'screen' => [],
                ],
                $metabox
            );
        }

        $this->remove = array_merge($this->remove, $metaboxes);
    }

    /**
     * Register and Remove Metaboxes.
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

    /**
     * Get Metabox value
     *
     * @param string $id Metabox ID
     * @param int $post Post ID. Defaults to the current post.
     * @return array
     */
    public function get($id, $post = null)
    {
        if ($item = $this->searchItems(['id' => $id], true)) {
            return $item->get($post);
        }

        return null;
    }

    /**
     * Set Metabox value
     *
     * @param string $id Metabox ID
     * @param mixed $value
     * @param int $post Post ID. Defaults to the current post.
     * @return bool
     */
    public function set($id, $value, $post = null)
    {
        if ($item = $this->searchItems(['id' => $id], true)) {
            return $item->set($value, $post);
        }

        return false;
    }

    /**
     * Save posted data.
     *
     * @param int $postId
     */
    public function save($postId) // todo add hooks
    {
        if (empty($_POST[$this->config['prefix']])) {
            return;
        }

        $form = $_POST[$this->config['prefix']];

        foreach ($this->items as $item) {
            $item->save($form, $postId);
        }
    }
}
