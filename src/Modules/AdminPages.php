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
    protected function __construct(App $app)
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

    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }
    }

    public function save($data)
    {
        if (empty($data['form'][$this->config['prefix']])) {
            return $this->m('Utils')->returnError('Form data is empty');
        }

        $form = $data['form'][$this->config['prefix']];

        if (empty($form['slug'])) {
            return $this->m('Utils')->returnError('Tab slug is empty');
        }

        foreach ($this->items as $item) {
            if ($tab = $item->findTab($form['slug'])) {
                return $tab->save($form);
            }
        }

        return $this->m('Utils')->returnError('Admin page tab not found');
    }
}
