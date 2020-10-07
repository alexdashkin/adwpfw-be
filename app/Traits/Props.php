<?php

namespace AlexDashkin\Adwpfw\Traits;

trait Props
{
    /**
     * @var array Item Props
     */
    protected $props = [];

    /**
     * Get Single Prop
     *
     * @param string $key
     * @return mixed
     */
    public function getProp(string $key)
    {
        return array_key_exists($key, $this->props) ? $this->props[$key] : $this->getDefault($key);
    }

    /**
     * Get All Props
     *
     * @return array
     */
    public function getProps(): array
    {
        $set = $default = [];

        foreach ($this->defaults() as $key => $value) {
            $default[$key] = $this->getDefault($key);
        }

        foreach ($this->props as $key => $value) {
            $set[$key] = $this->getProp($key);
        }

        return array_merge($default, $set);
    }

    /**
     * Set Single Prop
     *
     * @param string $key
     * @param mixed $value
     */
    public function setProp(string $key, $value)
    {
        $this->props[$key] = $value;
    }

    /**
     * Set Many Props
     *
     * @param array $data
     */
    public function setProps(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setProp($key, $value);
        }
    }

    /**
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        $defaults = $this->defaults();

        if (array_key_exists($key, $defaults)) {
            return is_callable($defaults[$key]) ? $defaults[$key]() : $defaults[$key];
        }

        return null;
    }

    /**
     * Get Default prop values, to be overridden
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [];
    }
}