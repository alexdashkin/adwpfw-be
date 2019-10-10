<?php

namespace AlexDashkin\Adwpfw\Modules\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Simple cache.
 */
class Cache extends Module
{
    /**
     * @var mixed Variable to store the Cache
     */
    private $cache;

    /**
     * Cache constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Get cached value if any.
     *
     * @param callable $callable Function to be cached.
     * @param array $args Args to be passed to the Function.
     * @return mixed Either cached result if any or the Function result.
     */
    public function get($callable, $args = [])
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