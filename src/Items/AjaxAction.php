<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Ajax Action (admin-ajax.php).
 */
class AjaxAction extends Ajax
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized $name.
     * @type string $name Action name without prefix (will be added automatically). Required.
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler. Gets an array with $_REQUEST params.
     * Must return array ['success', 'message', 'data']. Required.
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['name']),
            ],
            'name' => [
                'required' => true,
            ],
        ];

        parent::__construct($app, $data, $props);
    }

    /**
     * Handle the Request.
     *
     * @param array $request $_REQUEST params
     */
    public function run(array $request)
    {
        $this->log('Ajax request, action "%s"', [$this->data['name']]);

        $data = !empty($request['data']) ? $request['data'] : [];

        parent::handle($data);
    }
}
