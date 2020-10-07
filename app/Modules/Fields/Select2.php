<?php

namespace AlexDashkin\Adwpfw\Modules\Fields;

/**
 * label_cb, ajax_data_cb
 */
class Select2 extends Select
{
    /**
     * Init Module
     */
    public function init()
    {
        $ajaxDataCb = $this->getProp('ajax_data_cb');

        if ($ajaxDataCb && is_callable($ajaxDataCb)) {
            $action = sprintf('s2_%s', $this->getProp('name'));

            $this->args['ajax_action'] = $action;

            $this->m(
                'api.ajax',
                [
                    'prefix' => $this->prefix,
                    'action' => $action,
                    'fields' => [
                        'q' => [
                            'required' => true,
                        ]
                    ],
                    'callback' => [$this, 'ajaxDataCb'],
                ]
            );
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
            return $this->main->returnError('Empty query');
        }

        $minChars = (int)$this->getProp('min_chars');

        if (strlen($data['q']) < $minChars) {
            return $this->main->returnError('Minimum chars for search - ' . $minChars);
        }

        $results = $this->getProp('ajax_data_cb')(trim($data['q']));

        $return = [];

        foreach ($results as $value => $label) {
            $return[] = [
                'id' => $value,
                'text' => $label,
            ];
        }

        return $this->main->returnSuccess('Done', $return);
    }

    /**
     * Prepare Template Args
     */
    protected function prepareArgs()
    {
        parent::prepareArgs();

        $args = $this->args;

        $args['min_chars'] = $args['min_chars'] ?? 3;
        $args['min_items_for_search'] = $args['min_items_for_search'] ?? 10;
        $value = $args['value'];
        $multiple = !empty($args['multiple']);

        $valueArr = $multiple ? (array)$value : [$value];

        foreach ($valueArr as $item) {
            if ($item && !$this->main->arraySearch($args['options'], ['value' => $item], true)) {
                $labelCb = $this->getProp('label_cb');

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
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        $defaults = [
            'label_cb' => function () {
                return [$this, 's2PostLabel'];
            },
        ];

        return array_merge(parent::defaults(), $defaults);
    }
}
