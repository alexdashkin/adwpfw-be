<?php

namespace AlexDashkin\Adwpfw\_Fields;

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

    /**
     * Get Args for Twig Template
     *
     * @param mixed $value
     * @return array
     */
    public function getTwigArgs($value): array
    {
        $args = parent::getTwigArgs($value);

        $multiple = $this->getProp('multiple');

        $valueArr = $multiple ? (array)$value : [$value];



        foreach ($valueArr as $item) {
            if (!$this->m('helpers')->arraySearch($args['options'], ['value' => $item])) {
                $args['options'][] = [
                    'label' => !empty($this->getProp('label_cb')) ? $this->getProp('label_cb')($item) : $item,
                    'value' => $item,
                    'selected' => 'selected',
                ];
            }
        }

        return $args;
    }
}
