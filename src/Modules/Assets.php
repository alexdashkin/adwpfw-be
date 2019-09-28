<?php

namespace AlexDashkin\Adwpfw\Common;

/**
 * CSS/JS enqueuing
 */
class Assets extends Base
{
    private $adminCss = [];
    private $adminJs = [];
    private $frontCss = [];
    private $frontJs = [];
    private $remove = [];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdmin'], 20);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFront'], 20);
        add_filter('script_loader_tag', [$this, 'async'], 20);
    }

    /**
     * Add Admin CSS items
     *
     * @param array $list {
     * @type string $url
     * @type string $ver Version
     * @type array $deps Dependencies slugs
     * }
     */
    public function adminCss(array $list)
    {
        $this->register('adminCss', $list);
    }

    /**
     * Add Front CSS items
     *
     * @param array $list {
     * @type string $url
     * @type string $ver Version
     * @type array $deps Dependencies slugs
     * }
     */
    public function frontCss(array $list)
    {
        $this->register('frontCss', $list);
    }

    /**
     * Add Admin JS items
     *
     * @param array $list {
     * @type string $url
     * @type string $ver Version
     * @type array $deps Dependencies slugs
     * @type bool $async
     * @type array $localize Vars to be passed to the script
     * }
     */
    public function adminJs(array $list)
    {
        $this->js('adminJs', $list);
    }

    /**
     * Add Front JS items
     *
     * @param array $list {
     * @type string $url
     * @type string $ver Version
     * @type array $deps Dependencies slugs
     * @type bool $async
     * @type array $localize Vars to be passed to the script
     * }
     */
    public function frontJs(array $list)
    {
        $this->js('frontJs', $list);
    }

    /**
     * Remove assets
     *
     * @param array $ids Registered assets IDs to be removed
     */
    public function remove(array $ids)
    {
        $this->remove = array_merge($this->remove, $ids);
    }

    private function js($type, array $list)
    {
        $prefix = $this->config['prefix'];

        foreach ($list as &$item) {
            $item = array_merge([
                'deps' => ['jquery'],
                'async' => false,
            ], $item);

            if (!empty($item['file'])) {
                $item['localize'] = array_merge([
                    'prefix' => $prefix,
                    'debug' => !empty($this->config['debug']),
                    'nonce' => '',
                ], !empty($item['localize']) ? $item['localize'] : []);
            }
        }

        $this->register($type, $list);
    }

    private function register($type, array $list)
    {
        $version = $url = null;

        foreach ($list as &$item) {

            if (!empty($item['file'])) {
                $file = $item['file'];

                $url = $this->config['baseUrl'] . $file;

                $path = $this->config['baseDir'] . $file;

                if (file_exists($path)) {
                    $version = filemtime($path);
                }

                unset($list['file']);
            }

            $item = array_merge([
                'id' => strtolower($type),
                'ver' => $version,
                'deps' => [],
                'url' => $url,
            ], $item);

            $item['id'] = $this->config['prefix'] . '-' . $item['id'];
        }

        $this->$type = array_merge($this->$type, $list);
    }

    public function enqueueAdmin()
    {
        $this->enqueue($this->adminCss, $this->adminJs);
    }

    public function enqueueFront()
    {
        $this->enqueue($this->frontCss, $this->frontJs);
    }

    private function enqueue($css, $js)
    {
        foreach ($this->remove as $item) {
            if (wp_script_is($item, 'registered')) {
                wp_deregister_script($item);
            }
        }

        foreach ($css as $item) {
            if (!empty($item['callback']) && !$item['callback']()) {
                continue;
            }

            if ('wp-media' === $item['id']) {
                wp_enqueue_media();
            } elseif (empty($item['url'])) {
                wp_enqueue_style($item['id']);
            } else {
                wp_enqueue_style($item['id'], $item['url'], $item['deps'], $item['ver']);
            }
        }

        foreach ($js as $item) {
            if (!empty($item['callback']) && !$item['callback']()) {
                continue;
            }

            if (empty($item['url'])) {
                wp_enqueue_script($item['id']);
                continue;
            }

            $url = $item['async'] ? $item['url'] . '#' . $item['async'] : $item['url'];

            wp_enqueue_script($item['id'], $url, $item['deps'], $item['ver'], true);

            $prefix = $this->config['prefix'];

            $item['localize']['nonce'] = wp_create_nonce($prefix);
            $item['localize']['restNonce'] = wp_create_nonce('wp_rest');

            wp_localize_script($item['id'], $prefix, $item['localize']);
        }
    }

    public function async($tag)
    {
        $cleanUrl = preg_replace('/#(async|defer)$/', '', $tag);

        if (!preg_match('/#(async|defer)$/', $tag, $matches)) {
            return $tag;
        } elseif (is_admin()) {
            return $cleanUrl;
        }

        return str_replace('src', $matches[1] . ' src', $cleanUrl);
    }
}
