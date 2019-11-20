<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\AjaxAction;

/**
 * Admin Ajax Actions.
 */
class Ajax extends ModuleAjax
{
    /**
     * @var AjaxAction[]
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        add_action('wp_loaded', [$this, 'run']);
    }

    /**
     * Add Ajax Action
     *
     * @param array $data
     *
     * @see AjaxAction::__construct();
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new AjaxAction($this->app, $data);
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

        if (!$action = $this->searchItems(['name' => $actionName], true)) {
            return;
        }

        if (!check_ajax_referer($prefix, false, false)) {
            $this->error('Wrong nonce!', true);
        }

        $action->run($request);
    }
}