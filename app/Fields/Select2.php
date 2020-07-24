<?php

namespace AlexDashkin\Adwpfw\Fields;

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
    protected function getInitialPropDefs(): array
    {
        return array_merge(
            parent::getInitialPropDefs(),
            [
                'tpl' => [
                    'default' => 'select2',
                ],
                'options' => [
                    'type' => 'array',
                    'default' => [],
                ],
                'ajax_action' => [
                    'type' => 'string',
                    'default' => null,
                ],
                'min_chars' => [
                    'type' => 'int',
                    'default' => 3,
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

        $valueArr = $this->gp('multiple') ? (array)$value : [$value];

        foreach ($valueArr as $item) {
            if (!$this->m('helpers')->arraySearch($args['options'], ['value' => $item])) {
                $args['options'][] = [
                    'label' => !empty($this->gp('label_cb')) ? $this->gp('label_cb')($item) : $item,
                    'value' => $item,
                    'selected' => 'selected',
                ];
            }
        }

        return $args;
    }
}
