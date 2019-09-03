<?php
/**
 * Project      : DevCrud
 * File Name    : DevCrudModelInterface.php
 * Author       : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/06/26 6:34 PM
 */

namespace TunnelConflux\DevCrud\Contracts;

use Illuminate\Database\Eloquent\Builder;
use TunnelConflux\DevCrud\Models\JoinModel;

interface DevCrudModel
{
    const UUID_NAME = "uuid";
    const SLUG_NAME = "slug";
    const SLUG_FROM = "title";
    const STATUS_NAME = "status";
    const ACTIVE_STATUS = 1;

    /**
     * @return array
     */
    public function getInputTypes(): array;

    /**
     * @param array $inputTypes
     */
    public function setInputTypes(array $inputTypes): void;

    public function getTableColumns();

    /**
     * @param int|string|null $ignore
     * @param string|null $parentModel
     *
     * @return array
     */
    public function getRelationalFields($ignore = null, $parentModel = null): array;

    /**
     * @param string $fieldName
     *
     * @return JoinModel|null
     */
    public function getRelationalModel($fieldName): JoinModel;

    /**
     * @param array $fields
     */
    public function setRelationalFields(array $fields = []): void;

    /**
     * @return array
     */
    public function getInfoItems(): array;

    /**
     * @return array
     */
    public function getRequiredItems(): array;

    /**
     * @param array $requiredItems
     */
    public function setRequiredItems(array $requiredItems): void;

    /**
     * @return array
     */
    public function getListColumns(): array;

    /**
     * @param array $listColumns
     */
    public function setListColumns(array $listColumns): void;

    /**
     * @return array
     */
    public function getSearchColumns(): array;

    /**
     * @param array $searchColumns
     */
    public function setSearchColumns(array $searchColumns): void;

    /**
     * The Items will be ignored from form validation
     *
     * @return array
     */
    public function getIgnoreItems(): array;

    /**
     * The Items will be ignored from form validation
     *
     * @param array $ignoreItems
     */
    public function setIgnoreItems(array $ignoreItems): void;

    /**
     * The Items will be ignored from form validation during update
     *
     * @return array
     */
    public function getIgnoreItemsOnUpdate(): array;

    /**
     * The Items will be ignored from form validation during update
     *
     * @param array $ignoreItemsOnUpdate
     *
     * @return self
     */
    public function setIgnoreItemsOnUpdate(array $ignoreItemsOnUpdate);

    /**
     * @return bool
     */
    public function getAutoSlug(): bool;

    /**
     * @param bool $generate
     *
     * @return self
     */
    public function setAutoSlug(bool $generate);

    /*****************************************
     ** Eloquent Model Scope Functions      **
     *****************************************/

    /**
     * @param Builder $query
     * @param string $data
     * @param string $column
     *
     * @return Builder
     */
    public function scopeOrderByWhereIn(Builder $query, $data, $column = 'id');

    /**
     * @param Builder $query
     * @param mixed $value
     * @param array $columns
     *
     * @return Builder
     */
    public function scopeSearchColumns(Builder $query, $value, array $columns = []);

    /**
     * @param Builder $query
     * @param int $active
     *
     * @return Builder
     */
    public function scopeActive(Builder $query, $active = null);
}
