<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\App;

class Db extends Module
{
    /**
     * Start a DB query
     *
     * @param string $table
     * @return Query
     */
    public function table(string $table): Query
    {
        $wpdb = $this->gp('wpdb');

        return App::get(
            'query',
            [
                'wpdb' => $wpdb,
                'table_prefix' => $wpdb->prefix,
            ]
        )->table($table);
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
