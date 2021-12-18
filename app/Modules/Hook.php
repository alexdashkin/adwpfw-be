<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Action/Filter
 */
class Hook extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        add_filter($this->getProp('tag'), [$this, 'run'], $this->getProp('priority'), 100);
    }

    /**
     * Fire callback
     */
    public function run()
    {
        try {
            return $this->getProp('callback')(...func_get_args());
        } catch (\Exception $e) {
            $this->log('Exception in hook "%s": %s', [$this->getProp('tag'), $e->getMessage()]);
        }

        return false;
    }

    /**
     * Remove hook
     *
     * @return bool Removed?
     */
    public function remove(): bool
    {
        return remove_filter($this->getProp('tag'), [$this, 'run'], $this->getProp('priority'));
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
            'tag' => [
                'type' => 'string',
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

        return array_merge($baseProps, $fieldProps);
    }
}
