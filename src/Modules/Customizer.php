<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\AdminPage;

/**
 * Admin Settings pages
 */
class Customizer extends ModuleWithItems
{
    /**
     * @var AdminPage[]
     */
    protected $items = [];

    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        add_action('admin_menu', [$this, 'register']);

        $this->m('Ajax')->add([
            'name' => 'save',
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
     * Add Admin Page
     *
     * @param array $data
     *
     * @see AdminPage::__construct();
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new AdminPage($this->app, $data); // todo access to values from outside
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

        foreach ($this->items as $item) {
            $item->save($form);
        }

        return Helpers::returnSuccess();
    }
}
