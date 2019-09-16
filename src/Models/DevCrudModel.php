<?php
/**
 * Project      : DevCrud
 * File Name    : DevCrudModel.php
 * Author       : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/06/26 6:34 PM
 */

namespace TunnelConflux\DevCrud\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use TunnelConflux\DevCrud\Contracts\DevCrudModel as DevCrudModelContract;
use TunnelConflux\DevCrud\Helpers\DevCrudHelper as Helper;
use TunnelConflux\DevCrud\Models\Enums\InputTypes;
use TunnelConflux\DevCrud\Models\Enums\JoinTypes;

class DevCrudModel extends Model implements DevCrudModelContract
{
    public $inputTypes = [
        InputTypes::FILE => ['cv', 'attachment'],
        InputTypes::IMAGE => [
            'cover',
            'image',
            'thumb',
            'picture',
            'thumbnail',
            'thumb_image',
            'cover_sd',
            'image_sd',
            'thumb_sd',
            'thumbnail_sd',
            'thumb_image_sd',
            'meta_image',
        ],
        InputTypes::VIDEO => ['video',],
        InputTypes::TEXTAREA => [
            'description',
            'short_description',
            'content',
            'short_content',
            'text',
            'short_text',
            'address',
            'meta_description',
            'message',
        ],
        InputTypes::SELECT => ['status'],
        InputTypes::YES_NO => [],
    ];

    protected $infoItems = [];
    protected $requiredItems = [];
    protected $readOnlyItems = [];
    protected $ignoreItems = [];
    protected $ignoreItemsOnUpdate = [];
    /* @var JoinModel[] */
    protected $relationalFields = [];
    protected $listColumns = [];
    protected $searchColumns = [];
    protected $autoSlug = true;
    protected $refreshSlug = true;

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
                $item->{self::SLUG_NAME} = Helper::makeSlug($item, $item->{self::SLUG_FROM});
            }
        });

        static::updating(function (self $item) {
            if (Schema::hasColumn($item->getTable(), self::SLUG_NAME) && $item->refreshSlug) {
                $item->{self::SLUG_NAME} = Helper::makeSlug($item, $item->{self::SLUG_FROM});
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
            $model = app($field->getModel());

            if (in_array($field->getJoinType(), [JoinTypes::OneToOne, JoinTypes::OneToMany]) && $parentModel == $field->getModel()) {
                $model = $model->where($field->getIgnoreKey(), '!=', $ignore);
            }

            foreach ($field->getScopes() as $scope) {
                $model = $model->{$scope}();
            }

            if ($field->getWith()) {
                $data = $items[$key] = $model->with([$field->getWith()])->get();
                $items[$key] = $data->mapWithKeys(function ($val) use ($field) {
                    return [$val->{$field->getSelectKey()} => "{$val->{$field->getWith()}->{$field->getWithDisplayKey()}} - {$val->{$field->getDisplayKey()}}"];
                });
            } else {
                $items[$key] = $model->pluck($field->getDisplayKey(), $field->getSelectKey());
            }
        }

        //dd($model->toSql());

        return $items;
    }

    /**
     * @param string $fieldName
     *
     * @return JoinModel|null
     */
    public function getRelationalModel($fieldName): JoinModel
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
        return empty($this->infoItems) ? $this->fillable : $this->infoItems;
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

    public function getFormAble()
    {
        $formItems = [];
        $types = $this->inputTypes;
        $dataItems = $this->requiredItems;

        if (count($this->requiredItems) < 1) {
            $dataItems = $this->fillable;
        }

        /*foreach ($this->infoItems as $item) {
            $title = ucwords(str_replace('_', ' ', $item));

            if (in_array($item, $types[InputTypes::TEXTAREA])) {
                $formItems[$item] = new Formable($title, $item, InputTypes::TEXTAREA, $this->{$item});
            } elseif (in_array($item, $types[InputTypes::SELECT])) {
                $formItems[$item] = (new Formable($title, $item, InputTypes::SELECT, $this->{$item}))
                    ->setOptions(getStatus())
                    ->setDisable(true);
            } else {
                $formItems[$item] = (new Formable($title, $item, InputTypes::TEXT, $this->{$item}))->setDisable(true);
            }
        }*/

        foreach ($dataItems as $item) {
            /*if (in_array($item, $this->infoItems)) {
                continue;
            }*/

            $title = ucwords(str_replace('_', ' ', $item));

            if (in_array($item, $types[InputTypes::FILE])) {
                $formItems[$item] = (new Formable($title, $item, InputTypes::FILE));
            } elseif (in_array($item, $types[InputTypes::IMAGE])) {
                $formItems[$item] = (new Formable($title, $item, InputTypes::IMAGE));
            } elseif (in_array($item, $types[InputTypes::VIDEO])) {
                $formItems[$item] = (new Formable($title, $item, InputTypes::VIDEO));
            } elseif (in_array($item, $types[InputTypes::TEXTAREA])) {
                $formItems[$item] = (new Formable($title, $item, InputTypes::TEXTAREA, $this->{$item}));
            } elseif (in_array($item, $types[InputTypes::SELECT])) {
                $formItems[$item] = (new Formable($title, $item, InputTypes::SELECT, $this->{$item}))
                    ->setOptions(Helper::getStatus());
            } elseif (in_array($item, $types[InputTypes::YES_NO])) {
                $formItems[$item] = (new Formable($title, $item, InputTypes::SELECT, $this->{$item}))
                    ->setOptions(Helper::getYesNo());
            } else {
                $formItems[$item] = (new Formable($title, $item, InputTypes::TEXT, $this->{$item}));
            }
        }

        //dd($this->getRelationalFields(),$this->relationalFields);

        foreach ($this->getRelationalFields() as $key => $item) {
            $options = $item->toArray();
            $multiple = false;
            $title = ucwords(str_replace('_', ' ', $key));
            $joinModel = $this->getRelationalModel($key);

            if ($joinModel->getJoinType() == JoinTypes::ManyToMany) {
                $multiple = true;
            } elseif ($joinModel->getJoinType() == JoinTypes::OneToMany) {
                $key = Str::snake(Str::singular($key)) . "_id";
                Helper::arrayMerge($options, ["" => "Select an option"], true);
            }

            /*$options = app($item->getModel())->get();

            if (!empty($item->getOptionPrefix())) {
                $elements = $item->getOptionPrefix();
                $prefixOptions = [];

                if (isset($elements['join']) && isset($elements['key'])) {
                    foreach ($options as $option) {
                        $name = $option->{$item->getDisplayKey()};
                        $prefix = $option->{$elements['join']}->{$elements['key']} ?? "";
                        $prefixOptions[$option->{$item->getSelectKey()}] = trim("$prefix - $name", '- ');
                    }
                }

                $options = $prefixOptions;
            } else {
                $options = $options->pluck($item->getDisplayKey(), $item->getSelectKey())->toArray();
            }*/

            $formItems[$key] = (new Formable($title, $key, InputTypes::SELECT, $this->{$key}))
                ->setOptions($options)
                ->setMultiple($multiple);
        }

        return $formItems;
    }

    /*****************************************
     **   Eloquent Model Scope Functions    **
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
        $data = Helper::explodeString($data);

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
     * @param string  $column
     * @param mixed   $starting
     * @param mixed   $ending
     *
     * @return Builder
     */
    public function scopeInRange(Builder $query, string $column, $starting, $ending)
    {
        return $query->whereBetween($column, [$starting, $ending]);
    }

    /**
     * @param Builder $query
     * @param int     $active
     *
     * @return Builder
     */
    public function scopeActive(Builder $query, $active = null)
    {
        return (static::STATUS_NAME ? $query->where(static::STATUS_NAME, $active ?: static::ACTIVE_STATUS) : $query);
    }
}
