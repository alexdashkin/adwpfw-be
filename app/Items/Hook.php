<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\Module;

class Hook extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->validateData();

        add_filter($this->get('tag'), [$this, 'run'], $this->get('priority'), 100);
    }

    /**
     * Fire callback
     */
    public function run()
    {
        try {
            return $this->get('callback')(...func_get_args());
        } catch (\Exception $e) {
            $this->log('Exception in hook "%s": %s', [$this->get('tag'), $e->getMessage()]);
        }

        return false;
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'tag' => [
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'priority' => [
                'type' => 'int',
                'default' => 10,
            ],
        ];
    }
}
