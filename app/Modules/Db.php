<?php

namespace AlexDashkin\Adwpfw\Modules;

class Db extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->validateData();
    }

    /**
     * Start a DB query
     *
     * @param string $table
     * @return Query
     */
    public function table(string $table = ''): Query
    {
        $wpdb = $this->getProp('wpdb');

        return $this->m(
            'query',
            [
                'wpdb' => $wpdb,
                'table_prefix' => $wpdb->prefix,
            ]
        )->table($table);
    }

    /**
     * Get Prefixed Table Name
     *
     * @param string $name
     * @return string
     */
    public function getTableName(string $name): string
    {
        $wpdb = $this->getProp('wpdb');

        return !empty($wpdb->$name) ? $wpdb->$name : $this->getProp('table_prefix') . $name;
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
    {
        return [
            'wpdb' => [
                'type' => 'object',
                'required' => true,
            ],
            'table_prefix' => [
                'default' => function ($data) {
                    return $data['wpdb']->prefix;
                },
            ],
        ];
    }
}
