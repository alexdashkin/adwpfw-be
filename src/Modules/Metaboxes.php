<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Common\Helpers;

/**
 * Posts Metaboxes
 */
class Metaboxes extends ItemsModule
{
    private $metaboxes = [];
    private $remove = [];

    private $prefix;

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        $this->prefix = $this->config['prefix'];
        add_action('add_meta_boxes', [$this, 'register'], 20, 0);
        add_action('save_post', [$this, 'save']);
    }

    /**
     * Add a Metabox
     *
     * @param array $metabox {
     * @type string $id
     * @type string $prefix
     * @type string $title
     * @type array $screen For which Post Types to show
     * @type string $context
     * @type string $priority
     * @type array $options Fields to be printed
     * }
     */
    public function addMetabox(array $metabox)
    {
        $metabox = array_merge([
            'id' => '',
            'prefix' => $this->prefix,
            'title' => 'Metabox',
            'screen' => ['post', 'page'],
            'context' => 'normal',
            'priority' => 'default',
            'options' => [],
        ], $metabox);

        $metabox['id'] = $metabox['id'] ?: sanitize_title($metabox['title']);

        $this->metaboxes[] = $metabox;
    }

    /**
     * Add multiple Metaboxes
     *
     * @param array $metaboxes
     *
     * @see Metaboxes::addMetabox()
     */
    public function addMetaboxes(array $metaboxes)
    {
        foreach ($metaboxes as $metabox) {
            $this->addMetabox($metabox);
        }
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

    /**
     * Get a Metabox Value
     *
     * @param string $id Metabox ID without prefix
     * @param int|null $post Post ID (defaults to the current post)
     * @return mixed
     */
    public function get($id, $post = null)
    {
        if (!$post = get_post($post)) {
            return '';
        }

        return get_post_meta($post->ID, '_' . $this->prefix . '_' . $id, true);
    }

    /**
     * Set a Metabox Value
     *
     * @param string $id Metabox ID without prefix
     * @param mixed $value Value to set
     * @param int|null $post Post ID (defaults to the current post)
     * @return bool
     */
    public function set($id, $value, $post = null)
    {
        if (!$post = get_post($post)) {
            return '';
        }

        return update_post_meta($post->ID, '_' . $this->prefix . '_' . $id, $value);
    }

    public function register()
    {
        foreach ($this->metaboxes as $metabox) {
            add_meta_box(
                $this->prefix . '_' . $metabox['id'],
                $metabox['title'],
                function ($post) use ($metabox) {
                    $this->render($metabox, $post);
                },
                $metabox['screen'],
                $metabox['context'],
                $metabox['priority']
            );
        }

        foreach ($this->remove as $item) {
            foreach (['normal', 'side', 'advanced'] as $context) {
                remove_meta_box($item['id'], $item['screen'], $context);
            }
        }
    }

    public function save($postId) // todo add hooks
    {
        $prefix = $this->prefix;
        if (empty($_POST[$prefix])) {
            return;
        }

        foreach ($this->metaboxes as $metabox) {
            $id = $metabox['id'];
            if (!empty($_POST[$prefix][$id])) {
                $data = $_POST[$prefix][$id];
                do_action('adwpfw_metabox_saved', $postId, $id, $data);
                update_post_meta($postId, '_' . $prefix . '_' . $id, $data);
            }
        }
    }

    private function render($metabox, $post)
    {
        $options = $metabox['options'];
        $values = get_post_meta($post->ID, '_' . $this->prefix . '_' . $metabox['id'], true);
        $content = '';

        foreach ($options as $option) {

            $option['prefix'] = $this->prefix . '[' . $metabox['id'] . ']';

            if (isset($option['id'], $values[$option['id']])) {
                $value = $values[$option['id']];
            } else {
                $value = isset($option['default']) ? $option['default'] : '';
            }

            $option['value'] = $value;

            switch ($option['type']) {
                case 'checkbox':
                    $option['checked'] = !empty($value) ? ' checked ' : '';
                    $content .= $this->twig('checkbox', $option);
                    break;

                case 'select':
                    $items = [];

                    $placeholder = !empty($option['placeholder']) ? $option['placeholder'] : '--- Select ---';

                    $items[] = [
                        'label' => $placeholder,
                        'value' => '',
                        'selected' => '',
                    ];

                    $options = !empty($option['options']) ? $option['options'] : [];
                    $multiple = !empty($option['multiple']);

                    foreach ($options as $val => $label) {
                        $selected = $multiple ? in_array($val, (array)$value) : $val == $value;

                        $items[] = [
                            'label' => $label,
                            'value' => $val,
                            'selected' => $selected ? ' selected ' : '',
                        ];
                    }

                    $option['items'] = $items;

                    $content .= $this->twig($option['type'], $option);
                    break;

                case 'select2':
                    $items = [];

                    $placeholder = !empty($option['placeholder']) ? $option['placeholder'] : '--- Select ---';

                    $items[] = [
                        'label' => $placeholder,
                        'value' => '',
                        'selected' => '',
                    ];

                    $options = !empty($option['options']) ? $option['options'] : [];
                    $multiple = !empty($option['multiple']);

                    foreach ($options as $val => $label) {
                        $selected = $multiple ? in_array($val, (array)$value) : $val == $value;

                        $items[] = [
                            'label' => $label,
                            'value' => $val,
                            'selected' => $selected ? ' selected ' : '',
                        ];
                    }

                    $valueArr = $multiple ? (array)$value : [$value];

                    foreach ($valueArr as $item) {
                        if (!Helpers::arraySearch($items, ['value' => $item])) {
                            $items[] = [
                                'label' => !empty($option['label_cb']) ? $option['label_cb']($item) : $item,
                                'value' => $item,
                                'selected' => 'selected',
                            ];
                        }
                    }

                    $option['items'] = $items;

                    $content .= $this->twig('select2', $option);
                    break;

                case 'callback':
                    $content .= $option['callback']();
                    break;

                default:
                    $content .= $this->twig($option['type'], $option);
                    break;
            }
        }

        echo $this->twig('metabox', ['prefix' => $this->prefix, 'content' => $content]);
    }

    private function twig($name, $args = [])
    {
        return $this->m('Utils')->renderTwig($name, $args);
    }
}
