<?php

namespace AlexDashkin\Adwpfw\Modules\Fields;

/**
 * name*, id, tpl, label, placeholder, desc, required, default, classes
 */
class Checkbox extends Field
{
    /**
     * Prepare Template Args
     */
    protected function prepareArgs()
    {
        parent::prepareArgs();

        $this->args['checked'] = !empty($this->args['value']) ? 'checked' : '';
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        $defaults = [
            'checked' => false,
        ];

        return array_merge(parent::defaults(), $defaults);
    }
}
