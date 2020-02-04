<?php
/**
 * Project      : Loreal TMR Automation
 * File Name    : SearchInRelation.php
 * Author       : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2020/02/04 1:36 PM
 */

namespace TunnelConflux\DevCrud\Models;

/**
 * @property string   $name
 * @property string[] $columns
 */
class SearchInRelation
{
    /**
     * Name of the relation
     *
     * @var string
     */
    private $relationName = "";
    /**
     * Columns have to search
     *
     * @var string[]
     */
    private $columnsToSearch = [];

    public function __construct(string $name, array $columns)
    {
        $this->relationName = $name;
        $this->columnsToSearch = $columns;
    }

    private function name()
    {
        return $this->relationName;
    }

    private function columns()
    {
        return $this->columnsToSearch;
    }

    public function __get($key)
    {
        if (in_array($key, ['name', 'columns'])) {
            return $this->{$key}();
        }

        return $this->{$key};
    }
}
