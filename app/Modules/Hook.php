<?php

namespace AlexDashkin\Adwpfw\Modules;

class Hook extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->validateData();

        add_filter($this->gp('tag'), [$this, 'run'], $this->gp('priority'), 100);
    }

    /**
     * Fire callback
     */
    public function run()
    {
        try {
            return $this->gp('callback')(...func_get_args());
        } catch (\Exception $e) {
            $this->log('Exception in hook "%s": %s', [$this->gp('tag'), $e->getMessage()]);
        }

        return false;
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
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
