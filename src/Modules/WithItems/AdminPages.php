<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\WithItems\AdminPage;
use AlexDashkin\Adwpfw\Modules\Basic\Helpers;

/**
 * Admin Settings pages
 */
class AdminPages extends ModuleWithItems
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
     * Add Admin Page
     *
     * @param array $data
     * @param App $app
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     *
     * @see AdminPage::__construct();
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new AdminPage($data, $app);
    }

    /**
     * Init the Module
     */
    protected function init()
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

    /**
     * Register Admin Pages
     */
    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }
    }

    /**
     * Save Admin Page posted data.
     *
     * @param array $data Posted data.
     * @return array Success or Error array to pass as Ajax response.
     */
    public function save($data)
    {
        if (empty($data['form'][$this->config['prefix']])) {
            return Helpers::returnError('Form data is empty');
        }

        $form = $data['form'][$this->config['prefix']];

        if (empty($form['slug'])) {
            return Helpers::returnError('Tab slug is empty');
        }

        foreach ($this->items as $item) {
            if ($tab = $item->findTab($form['slug'])) {
                return $tab->save($form);
            }
        }

        return Helpers::returnError('Admin page tab not found');
    }
}
