<?php
/**
 * Project      : DevCrud
 * File Name    : DevCrudModel.php
 * Author         : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/06/26 6:34 PM
 */

namespace TunnelConflux\DevCrud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use TunnelConflux\DevCrud\Http\Interfaces\DevCrudModelInterface;

class DevCrudModel extends Model implements DevCrudModelInterface
{
    protected $inputTypes = [
        'file'     => ['cv',],
        'image'    => ['cover', 'image', 'thumb', 'thumbnail', 'thumb_image', 'cover_sd', 'image_sd', 'thumb_sd', 'thumbnail_sd', 'thumb_image_sd', 'meta_image'],
        'video'    => ['video',],
        'textarea' => ['description', 'short_description', 'content', 'short_content', 'text', 'short_text', 'meta_description'],
        'select'   => ['status'],
    ];

    protected $infoItems           = [];
    protected $requiredItems       = [];
    protected $ignoreItems         = [];
    protected $ignoreItemsOnUpdate = [];
    /* @var \TunnelConflux\DevCrud\Models\JoinModel[] */
    protected $relationalFields = [];
    protected $listColumns      = [];
    protected $searchColumns    = [];
    protected $autoSlug         = true;
    protected $refreshSlug      = true;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $item) {
            $cols = $item->getTableColumns();

            if (in_array(self::UUID_NAME, $cols)) {
                $item->{self::UUID_NAME} = Str::orderedUuid()->toString();
            }

            if (in_array(self::SLUG_NAME, $cols) && $item->autoSlug) {
                $item->{self::SLUG_NAME} = getSlug($item, $item->{self::SLUG_FROM});
            }
        });

        static::updating(function (self $item) {
            if (Schema::hasColumn($item->getTable(), self::SLUG_NAME) && $item->refreshSlug) {
                $item->{self::SLUG_NAME} = getSlug($item, $item->{self::SLUG_FROM});
            }
        });
    }

    /**
     * @return array
     */
    public function getInputTypes(): array
    {
        return $this->inputTypes;
    }

    /**
     * @param array $inputTypes
     */
    public function setInputTypes(array $inputTypes): void
    {
        $this->inputTypes = $inputTypes;
    }

    public function getTableColumns()
    {
        return Schema::getColumnListing($this->getTable());
    }

    /**
     * @param int|string|null $ignore
     * @param string|null     $parentModel
     *
     * @return array
     */
    public function getRelationalFields($ignore = null, $parentModel = null): array
    {
        $items = [];

        foreach ($this->relationalFields as $key => $field) {
            $model = $field->getModel()::query();

            if (in_array($field->getJoinType(), ['oneToOne', 'oneToMany']) && $parentModel == $field->getModel()) {
                $model->where($field->getIgnoreKey(), '!=', $ignore);
            }

            foreach ($field->getScopes() as $scope) {
                $model->{$scope}();
            }

            if ($field->getWith()) {
                $data        = $items[$key] = $model->with([$field->getWith()])->get();
                $items[$key] = $data->mapWithKeys(function ($val) use ($field) {
                    return [$val->{$field->getSelectKey()} => "{$val->{$field->getWith()}->{$field->getWithDisplayKey()}} - {$val->{$field->getDisplayKey()}}"];
                });
            } else {
                $items[$key] = $model->pluck($field->getDisplayKey(), $field->getSelectKey());
            }
        }

        return $items;
    }

    /**
     * @param string $fieldName
     *
     * @return \TunnelConflux\DevCrud\Models\JoinModel|null
     */
    public function getFormRelationalModel($fieldName): JoinModel
    {
        return $this->relationalFields[$fieldName] ?? null;
    }

    /**
     * @param array $fields
     */
    public function setRelationalFields(array $fields = []): void
    {
        $this->relationalFields = $fields;
    }

    /**
     * @return array
     */
    public function getInfoItems(): array
    {
        return $this->infoItems;
    }

    /**
     * @return array
     */
    public function getRequiredItems(): array
    {
        return $this->requiredItems;
    }

    /**
     * @param array $requiredItems
     */
    public function setRequiredItems(array $requiredItems): void
    {
        $this->requiredItems = $requiredItems;
    }

    /**
     * @return array
     */
    public function getListColumns(): array
    {
        return $this->listColumns;
    }

    /**
     * @param array $listColumns
     */
    public function setListColumns(array $listColumns): void
    {
        $this->listColumns = $listColumns;
    }

    /**
     * @return array
     */
    public function getSearchColumns(): array
    {
        return $this->searchColumns;
    }

    /**
     * @param array $searchColumns
     */
    public function setSearchColumns(array $searchColumns): void
    {
        $this->searchColumns = $searchColumns;
    }

    /**
     * The Items will be ignored from form validation
     *
     * @return array
     */
    public function getIgnoreItems(): array
    {
        return $this->ignoreItems;
    }

    /**
     * The Items will be ignored from form validation
     *
     * @param array $ignoreItems
     */
    public function setIgnoreItems(array $ignoreItems): void
    {
        $this->ignoreItems = $ignoreItems;
    }

    /**
     * The Items will be ignored from form validation during update
     *
     * @return array
     */
    public function getIgnoreItemsOnUpdate(): array
    {
        if (!empty($this->ignoreItemsOnUpdate)) {
            return $this->ignoreItemsOnUpdate;
        }

        return $this->ignoreItems;
    }

    /**
     * The Items will be ignored from form validation during update
     *
     * @param array $ignoreItemsOnUpdate
     *
     * @return self
     */
    public function setIgnoreItemsOnUpdate(array $ignoreItemsOnUpdate): self
    {
        $this->ignoreItemsOnUpdate = $ignoreItemsOnUpdate;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoSlug(): bool
    {
        return $this->autoSlug;
    }

    /**
     * @param bool $generate
     *
     * @return self
     */
    public function setAutoSlug(bool $generate): self
    {
        $this->autoSlug = $generate;

        return $this;
    }

    /*****************************************
     ** Eloquent Model Scope Functions      **
     *****************************************/

    /**
     * @param Builder $query
     * @param string  $data
     * @param string  $column
     *
     * @return Builder
     */
    public function scopeOrderByWhereIn(Builder $query, $data, $column = 'id')
    {
        $data = trimArray(explodeString($data));

        if (count($data) > 0) {
            $query->orderByRaw("FIELD({$column}, " . implode(',', $data) . ")");
        }

        return $query->whereIn($column, $data);
    }

    /**
     * @param Builder $query
     * @param mixed   $value
     * @param array   $columns
     *
     * @return Builder
     */
    public function scopeSearchColumns(Builder $query, $value, array $columns = [])
    {
        $columns = empty($columns) ? $this->searchColumns : $columns;

        if (empty($columns)) {
            $columns = array_unique(array_merge($this->fillable, $this->infoItems));
        }

        return $query->where(function ($q) use ($columns, $value) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'LIKE', "{$value}%");
            }
        });
    }

    /**
     * @param Builder $query
     * @param int     $active
     *
     * @return Builder
     */
    public function scopeActive(Builder $query, $active = null)
    {
        return (self::STATUS_NAME ? $query->where(self::STATUS_NAME, $active ?: self::ACTIVE_STATUS) : $query);
    }
}