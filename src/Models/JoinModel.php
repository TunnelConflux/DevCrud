<?php

namespace TunnelConflux\DevCrud\Models;

/**
 * Class JoinModel
 *
 * @package TunnelConflux\DevCrud\Models
 */
class JoinModel
{
    protected $model;
    protected $selectKey;
    protected $displayKey;
    protected $ignoreKey;
    protected $joinType;
    protected $with;
    protected $withDisplayKey;
    protected $scopes;

    /**
     * JoinModel constructor.
     *
     * @param string      $model
     * @param string      $selectKey
     * @param string      $displayKey
     * @param null|string $ignoreKey
     * @param string      $joinType
     * @param null|string $with
     * @param null|string $withDisplayKey
     * @param array       $scopes
     */
    public function __construct(
        $model,
        $selectKey,
        $displayKey,
        $ignoreKey = null,
        $joinType = 'oneToOne',
        array $scopes = [],
        $with = null,
        $withDisplayKey = "title"
    )
    {
        $this->model          = $model;
        $this->selectKey      = $selectKey;
        $this->displayKey     = $displayKey;
        $this->ignoreKey      = $ignoreKey ?? $selectKey;
        $this->joinType       = $joinType;
        $this->with           = $with;
        $this->withDisplayKey = $withDisplayKey;
        $this->scopes         = $scopes;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $model
     *
     * @return \App\Models\Crud\JoinModel
     */
    public function setModel($model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSelectKey()
    {
        return $this->selectKey;
    }

    /**
     * @param $selectKey
     *
     * @return \App\Models\Crud\JoinModel
     */
    public function setSelectKey($selectKey): self
    {
        $this->selectKey = $selectKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDisplayKey()
    {
        return $this->displayKey;
    }

    /**
     * @param $displayKey
     *
     * @return \App\Models\Crud\JoinModel
     */
    public function setDisplayKey($displayKey): self
    {
        $this->displayKey = $displayKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIgnoreKey()
    {
        return $this->ignoreKey;
    }

    /**
     * @param $ignoreKey
     *
     * @return \App\Models\Crud\JoinModel
     */
    public function setIgnoreKey($ignoreKey): self
    {
        $this->ignoreKey = $ignoreKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getJoinType()
    {
        return $this->joinType;
    }

    /**
     * @param $joinType
     *
     * @return \App\Models\Crud\JoinModel
     */
    public function setJoinType($joinType): self
    {
        $this->joinType = $joinType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWith()
    {
        return $this->with;
    }

    /**
     * @param $with
     *
     * @return \App\Models\Crud\JoinModel
     */
    public function setWith($with): self
    {
        $this->with = $with;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWithDisplayKey()
    {
        return $this->withDisplayKey;
    }

    /**
     * @param $withDisplayKey
     *
     * @return \App\Models\Crud\JoinModel
     */
    public function setWithDisplayKey($withDisplayKey): self
    {
        $this->withDisplayKey = $withDisplayKey;

        return $this;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @param array|string $scopes
     * @param bool         $replace
     *
     * @return \App\Models\Crud\JoinModel
     */
    public function setScopes($scopes, $replace = false): self
    {
        if (is_string($scopes)) {
            $replace ? ($this->scopes = [$scopes]) : array_push($this->scopes, $scopes);
        } elseif (is_array($scopes)) {
            $replace ? ($this->scopes = $scopes) : ($this->scopes = array_merge($this->scopes, $scopes));
        }

        return $this;
    }
}