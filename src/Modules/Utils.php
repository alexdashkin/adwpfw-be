<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;

/**
 * Dynamic Helpers
 */
class Utils extends Module
{
    private $cache;

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Simple cache
     *
     * @param callable $callable
     * @param array $args
     * @return mixed
     */
    public function cache($callable, $args = [])
    {
        $cacheArgs = $args;

        foreach ($cacheArgs as $index => $arg) {
            if (!is_scalar($arg)) {
                $cacheArgs[$index] = maybe_serialize($arg);
            }
        }

        $funcName = is_array($callable) ? get_class($callable[0]) . $callable[1] : $callable;
        $cacheKey = md5($funcName . implode('', $cacheArgs));

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $result = call_user_func_array($callable, $args);

        $this->cache[$cacheKey] = $result;

        return $result;
    }
}