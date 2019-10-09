<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Admin Ajax Action
 */
class AjaxAction extends Ajax
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $action Action Slug without prefix (will be added automatically). Required.
     * @type callable $callback Handler. Required.
     * @type array $fields Accepted params [type, required]
     * }
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'action' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app, $props);
    }

    /**
     * Handle the Request
     */
    public function run($request)
    {
        $data = !empty($request['data']) ? $this->validateRequest($request['data']) : [];

        $result = call_user_func($this->data['callback'], $data);

        if (is_array($result)) {
            $return = array_merge(['success' => false, 'message' => '', 'data' => ''], $result);
        }

        return $return;
    }
}
