<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\AdminPage;

/**
 * Admin Settings pages
 */
class AdminPages extends ItemsModule
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
     * Add an Item
     *
     * @param array $data
     * @param App $app
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new AdminPage($data, $app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
        add_action('admin_menu', [$this, 'register']);

        $this->m('Ajax')->add([
            'action' => 'save',
            'fields' => [
                'form' => [
                    'type' => 'form',
                    'required' => true,
                ],
            ],
            'callback' => [$this, 'save'],
        ]);
    }

    public function save($data)
    {
        if (empty($data['form'][$this->config['prefix']])) {
            return $this->m('Utils')->returnError('Form data is empty');
        }

        foreach ($this->items as $item) {
            if ($return = $item->save($data)) {
                return $return;
            }
        }

        return $this->m('Utils')->returnError('Admin page not found');
    }
}
