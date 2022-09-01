<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\{Helpers, Modules\RestApi\AdminAjax};

/**
 * Select2 field
 */
class Select2 extends Select
{
    /**
     * Constructor
     */
    public function init()
    {
        $ajaxDataCb = $this->getProp('ajaxDataCallback');

        if ($ajaxDataCb && is_callable($ajaxDataCb)) {
            $action = sprintf('s2_%s', $this->getProp('name')); // todo 2 fields with the same name => race condition

            $this->args['ajax_action'] = $action;

            new AdminAjax([
                'action' => $action,
                'fields' => [
                    'q' => [
                        'required' => true,
                    ]
                ],
                'callback' => [$this, 'ajaxDataCb'],
            ], $this->app);
        }
    }

    /**
     * Ajax Data Callback
     *
     * @param array $data
     * @return array
     */
    public function ajaxDataCb(array $data): array
    {
        if (empty($data['q'])) {
            return $this->app->returnError('Empty query');
        }

        $minChars = (int)$this->getProp('min_chars');

        if (strlen($data['q']) < $minChars) {
            return $this->app->returnError('Minimum chars for search - ' . $minChars);
        }

        $results = $this->getProp('ajaxDataCallback')(trim($data['q']));

        $return = [];

        foreach ($results as $value => $label) {
            $return[] = [
                'id' => $value,
                'text' => $label,
            ];
        }

        return $this->app->returnSuccess('Done', $return);
    }

    /**
     * Prepare Template Args
     *
     * @param int $objectId
     */
    protected function prepareArgs(int $objectId = 0)
    {
        parent::prepareArgs($objectId);

        $args = $this->args;

        $args['min_chars'] = $args['min_chars'] ?? 3;
        $args['min_items_for_search'] = $args['min_items_for_search'] ?? 10;
        $value = $args['value'];
        $multiple = !empty($args['multiple']);

        $valueArr = $multiple ? (array)$value : [$value];

        foreach ($valueArr as $item) {
            if ($item && !$this->app->arraySearch($args['options'], ['value' => $item], true)) {
                $labelCb = $this->getProp('labelCallback');

                $args['options'][] = [
                    'label' => ($labelCb && is_callable($labelCb)) ? $labelCb($item) : $item,
                    'value' => $item,
                    'selected' => 'selected',
                ];
            }
        }

        $this->args = $args;
    }

    /**
     * Posts label Default Callback
     *
     * @param int $id
     * @return string
     */
    public function s2PostLabel($id): string
    {
        if (!$id || !$post = get_post($id)) {
            return (string)$id;
        }

        return $post->post_title . " (#$id)";
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        $baseProps = parent::getPropDefs();

        $fieldProps = [
            'options' => [
                'type' => 'array',
                'default' => [],
            ],
            'ajaxDataCallback' => [
                'type' => 'callable',
            ],
            'labelCallback' => [
                'type' => 'callable',
                'default' => function () {
                    return [$this, 's2PostLabel'];
                },
            ],
            'template' => [
                'type' => 'string',
                'default' => 'select2',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
