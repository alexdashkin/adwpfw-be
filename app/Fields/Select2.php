<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;

/**
 * Select2 Field
 */
class Select2 extends Select
{
    /**
     * Get Field props
     *
     * @return array
     */
    protected function props(): array
    {
        return array_merge(
            parent::props(),
            [
                'tpl' => [
                    'default' => 'select2',
                ],
                'ajax_action' => [
                    'type' => 'string',
                    'default' => null,
                ],
                'min_chars' => [
                    'type' => 'int',
                    'default' => 0,
                ],
                'min_items_for_search' => [
                    'type' => 'int',
                    'default' => 10,
                ],
                'label_cb' => [
                    'type' => 'callable',
                    'default' => null,
                ],
            ]
        );
    }

    public function getTwigArgs($value): array
    {
        $args = parent::getTwigArgs($value);

        $valueArr = $this->get('multiple') ? (array)$value : [$value];

        foreach ($valueArr as $item) {
            if (!App::get('helpers')->arraySearch($args['options'], ['value' => $item])) {
                $args['options'][] = [
                    'label' => !empty($this->get('label_cb')) ? $this->get('label_cb')($item) : $item,
                    'value' => $item,
                    'selected' => 'selected',
                ];
            }
        }

        return $args;
    }
}
