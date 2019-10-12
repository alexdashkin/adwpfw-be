<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Admin Ajax Action
 */
class AjaxAction extends Ajax
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized $name.
     * @type string $name Action name without prefix (will be added automatically). Required.
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler. Gets an array with $_REQUEST params.
     * Must return array ['success', 'message', 'data']. Required.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['name']),
            ],
            'name' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app, $props);
    }

    /**
     * Handle the Request.
     * @param array $request $_REQUEST params
     */
    public function run($request)
    {
        $this->log('Ajax request, action "%s"', [$this->data['name']]);

        $data = !empty($request['data']) ? $request['data'] : [];

        parent::handle($data);
    }
}
