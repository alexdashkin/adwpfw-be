<?php

namespace AlexDashkin\Adwpfw\Admin;

/**
 * Admin notices
 */
class Notices extends \AlexDashkin\Adwpfw\Common\Base
{
    private $notices = [];
    private $option;

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        $this->option = get_option($this->config['prefix'] . '_notices') ?: [];
        add_action('admin_notices', [$this, 'render']);
        add_action('shutdown', [$this, 'updateOption']);
    }

    /**
     * Add Admin Notice
     *
     * @param array $notice {
     * @type string $id
     * @type string $message Message to display (tpl will be ignored)
     * @type string $tpl Name of the notice TWIG template
     * @type string $type Notice type (success, error)
     * @type bool $dismissible Whether can be dismissed
     * @type bool $once Don't show after dismissed
     * @type string $classes Container classes
     * @type array $args Additional TWIG Args
     * }
     */
    public function addNotice(array $notice)
    {
        $notice = array_merge([
            'id' => '',
            'message' => '',
            'tpl' => '',
            'type' => 'success',
            'dismissible' => true,
            'once' => false,
            'classes' => '',
            'args' => [],
        ], $notice);

        $notice['tpl'] = $notice['tpl'] ?: $notice['id'];

        $this->notices[] = $notice;
    }

    /**
     * Add multiple Notices
     *
     * @param array $notices
     *
     * @see Notices::addNotice()
     */
    public function addNotices($notices)
    {
        foreach ($notices as $notice) {
            $this->addNotice($notice);
        }
    }

    /**
     * Show a notice
     *
     * @param string $id Notice ID
     */
    public function show($id)
    {
        unset($this->option[$id]);
    }

    /**
     * Stop showing a notice
     *
     * @param string $id Notice ID
     */
    public function stop($id)
    {
        $this->option[$id] = 0;
    }

    /**
     * Dismiss a notice
     *
     * @param string $id Notice ID
     */
    public function dismiss($id)
    {
        $this->option[$id] = time();
    }

    public function render()
    {
        foreach ($this->notices as $notice) {
            if (!empty($notice['callback']) && !$notice['callback']()) {
                continue;
            }

            if (!$notice['dismissible'] || ($notice['dismissible'] && !isset($this->option[$notice['id']]))) {
                echo $this->renderNotice($notice);
                continue;
            }

            if (!$time = (int)$this->option[$notice['id']]) {
                continue;
            }

            if ($notice['once']) {
                $this->option[$notice['id']] = 0;
            } elseif (!empty($notice['days'])) {
                if ($time < time() - $notice['days'] * DAY_IN_SECONDS) {
                    echo $this->renderNotice($notice);
                }
            }
        }
    }

    private function renderNotice($notice)
    {
        $classes = $notice['classes'];
        $classes .= ' notice notice-' . $notice['type'];

        if ($notice['dismissible']) {
            $classes .= ' is-dismissible';
        }

        $id = $notice['id'];
        $classes .= ' ' . $this->config['prefix'] . '-notice';

        if ($notice['message']) {
            return "<div class='$classes' data-id='$id'><p>{$notice['message']}</p></div>";
        } elseif ($notice['tpl']) {
            $notice['args']['id'] = $id;
            $notice['args']['classes'] = $classes;
            return $this->m('Common\Utils')->renderTwig('notices/' . $notice['tpl'], $notice['args']);
        }

        return '';
    }

    public function updateOption()
    {
        update_option($this->config['prefix'] . '_notices', $this->option);
    }
}
