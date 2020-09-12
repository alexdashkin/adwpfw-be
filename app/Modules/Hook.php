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
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        switch ($key) {
            case 'priority':
                return 10;
        }

        return null;
    }
}
