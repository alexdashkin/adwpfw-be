<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * tag*, callback*, priority
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
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'priority' => 10,
        ];
    }
}
