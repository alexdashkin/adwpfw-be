<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\AjaxAction;

/**
 * Admin Ajax Actions
 */
class Ajax extends ModuleAjax
{
    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add an item
     *
     * @param array $data
     * @param App $app
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new AjaxAction($data, $app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function init()
    {
        add_action('wp_loaded', [$this, 'run']);
    }

    /**
     * Handle the Request
     */
    public function run()
    {
        if (!wp_doing_ajax()) {
            return;
        }

        $prefix = $this->config['prefix'];

        $request = $_REQUEST;

        if (empty($request['action']) || false === strpos($request['action'], $prefix)) {
            return;
        }

        $actionName = str_ireplace($prefix . '_', '', $request['action']);

        if (!$action = $this->searchItems(['name' => $actionName])) {
            return;
        }

        if (!check_ajax_referer($prefix, false, false)) {
            $this->error('Wrong nonce!', true);
        }

        $this->log('Ajax request received, action: ' . $actionName);

        try {
            wp_send_json($action->run($request));

        } catch (\Exception $e) {
            $this->error($e->getMessage(), true);
        }
    }
}