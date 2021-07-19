<?php

namespace AlexDashkin\Adwpfw\Core;

class Query
{
    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @var string WP DB table prefix
     */
    private $tablePrefix;

    /**
     * The table which the query is targeting.
     *
     * @var string
     */
    private $table;

    /**
     * The columns that should be returned.
     *
     * @var array
     */
    private $columns = [];

    /**
     * Indicates if the query returns distinct results.
     *
     * @var bool
     */
    private $distinct = false;

    /**
     * The where constraints for the query.
     *
     * @var array
     */
    private $where = [];

    /**
     * The orderings for the query.
     *
     * @var array
     */
    private $orders = [];

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    private $limit = 0;

    /**
     * The number of records to skip.
     *
     * @var int
     */
    private $offset = 0;

    /**
     * Set table
     *
     * @param string $table
     */
    public function __construct(string $table = '')
    {
        $this->wpdb = $GLOBALS['wpdb'];

        $this->tablePrefix = $this->wpdb->prefix;

        if ($table) {
            $this->table = $this->tablePrefix . $table;
        }
    }

    /**
     * Set the columns to be selected
     *
     * @param array $columns
     * @return $this
     */
    public function columns(array $columns = []): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Force the query to only return distinct results.
     *
     * @return $this
     */
    public function distinct(): self
    {
        $this->distinct = true;

        return $this;
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param array $conditions
     * @param string $compare
     * @return $this
     */
    public function where(array $conditions, string $compare = 'and'): self
    {
        $this->where = [
            'conditions' => $conditions,
            'compare' => $compare,
        ];

        return $this;
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orders[] = [
            'column' => $column,
            'direction' => 'desc' === strtolower($direction) ? 'desc' : 'asc',
        ];

        return $this;
    }

    /**
     * Set the "offset" value of the query.
     *
     * @param int $value
     * @return $this
     */
    public function offset(int $value): self
    {
        $this->offset = max(0, $value);

        return $this;
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $value
     * @return $this
     */
    public function limit(int $value): self
    {
        if ($value >= 0) {
            $this->limit = $value;
        }

        return $this;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @return array
     */
    public function select(): array
    {
        $columns = implode('`,`', $this->columns);
        $columns = $columns ? '`' . $columns . '`' : '*';

        $query = sprintf('SELECT %s %s FROM `%s`', $this->distinct ? 'DISTINCT' : '', $columns, $this->table);

        $query .= $this->buildWhereClause();

        $query .= $this->buildOrderClause();

        $query .= $this->buildLimitClause();

        return $this->wpdb->get_results($query, 'ARRAY_A');
    }

    /**
     * Insert a new record into the database.
     *
     * @param array $values
     * @return int
     */
    public function insert(array $values): int
    {
        return $this->wpdb->insert($this->table, $values) ? $this->wpdb->insert_id : 0;
    }

    /**
     * Insert Multiple Rows with one query.
     *
     * @param array $data Rows to insert.
     * @return bool|int
     */
    public function insertRows(array $data)
    {
        $data = array_values($data);
        $values = [];
        $counter = 0;

        $firstRow = reset($data);
        $cols = array_keys($firstRow);
        $columns = '`' . implode('`, `', $cols) . '`';
        $placeholders = str_repeat('%s, ', count($firstRow));

        foreach ($data as $index => $row) {
            $values = array_merge($values, array_values($row));
            $counter++;
        }

        if (!$counter) {
            return 0;
        }

        $columns = trim($columns, ', ');
        $placeholders = '(' . trim($placeholders, ', ') . '), ';
        $query = sprintf('INSERT INTO `%s` (%s) VALUES %s', $this->table, $columns, trim(str_repeat($placeholders, $counter), ', '));

        return $this->query($query, $values);
    }

    /**
     * Get Last Insert ID.
     *
     * @return int
     */
    public function getLastInsertId()
    {
        return (int)$this->wpdb->insert_id;
    }

    /**
     * Update a record in the database.
     *
     * @param array $values
     * @return int
     */
    public function update(array $values)
    {
        return $this->wpdb->update($this->table, $values, $this->where['conditions']);
    }

    /**
     * Delete a record from the database.
     *
     * @return int
     */
    public function delete()
    {
        return $this->wpdb->delete($this->table, $this->where['conditions']);
    }

    /**
     * Run a truncate statement on the table.
     */
    public function truncate()
    {
        return $this->query('TRUNCATE ' . $this->table);
    }

    /**
     * Perform a DB Query.
     *
     * @param string $query SQL Query.
     * @param array $values If passed, $wpdb->prepare() will be called first. Default [].
     * @return bool|int
     */
    public function query(string $query, array $values = [])
    {
        $sql = $values ? $this->wpdb->prepare($query, $values) : $query;

        return $this->wpdb->query($sql);
    }

    /**
     * Perform get_results query
     *
     * @param string $query SQL Query.
     * @param array $values If passed, $wpdb->prepare() will be called first. Default [].
     * @return array
     */
    public function getResults(string $query, array $values = []): array
    {
        $sql = $values ? $this->wpdb->prepare($query, $values) : $query;

        return $this->wpdb->get_results($sql, 'ARRAY_A');
    }

    /**
     * Build where clause
     *
     * @return string
     */
    private function buildWhereClause(): string
    {
        if (!$this->where['conditions']) {
            return '';
        }

        $whereArr = [];

        foreach ($this->where['conditions'] as $field => $value) {
            $whereArr[] = sprintf('`%s`="%s"', $field, $value);
        }

        $whereClause = implode(" {$this->where['compare']} ", $whereArr);

        return ' WHERE ' . $whereClause;
    }

    /**
     * Build Order By clause
     *
     * @return string
     */
    private function buildOrderClause(): string
    {
        if (!$this->orders) {
            return '';
        }

        $orderArr = [];

        foreach ($this->orders as $order) {
            $orderArr[] = sprintf('`%s` %s', $order['column'], $order['direction']);
        }

        $orderClause = implode(', ', $orderArr);

        return ' ORDER BY  ' . $orderClause;
    }

    /**
     * Build Offset/Limit clause
     *
     * @return string
     */
    private function buildLimitClause(): string
    {
        if (!$this->offset && !$this->limit) {
            return '';
        }

        return sprintf(' LIMIT %d %d ', (int)$this->offset, (int)$this->limit);
    }
}
