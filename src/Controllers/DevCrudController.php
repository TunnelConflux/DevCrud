<?php

/**
 * Project      : DevCrud
 * File Name    : DevCrudController.php
 * Author       : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/06/26 6:21 PM
 */

namespace TunnelConflux\DevCrud\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use TunnelConflux\DevCrud\Helpers\DevCrudHelper as Helper;
use TunnelConflux\DevCrud\Models\DevCrudModel;
use TunnelConflux\DevCrud\Traits\DevCrudTrait;

class DevCrudController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, DevCrudTrait;

    const ACTION_SHOW   = 'show';
    const ACTION_CREATE = 'create';
    const ACTION_STORE  = 'store';
    const ACTION_EDIT   = 'edit';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    public $data;
    public $page;
    public $pageTitle;
    public $routePrefix;
    public $viewPrefix;

    public $formTitle;
    /**
     * Title, input_type, DB_column,
     * Title, input_type, DB_column, select_items, //Drop-down
     *
     * @var array
     */
    public $formItems;
    public $formActionId;
    public $formActionRoute;
    public $formActionMethod;
    public $formIgnoreItems;
    public $formRequiredItems = [];
    public $formIgnoreItemsOnUpdate;
    public $formHasParents;

    public $paginate;
    public $uploadPath;
    public $actionMessage;

    public $isCreatable = true;
    public $isEditable = true;
    public $isViewable = true;
    public $isDeletable = true;
    public $infoItems;
    public $listColumns;
    public $itemPerPage = 15;

    protected $homeRoute = "dashboard";
    protected $redirectAfterAction = true;

    /**
     * @var DevCrudModel
     */
    protected $model;

    public function __construct(DevCrudModel $model)
    {
        $route = Route::currentRouteName();
        $routeParts = Helper::explodeString(Str::snake($route), '.');

        $this->model = $model;
        $this->page = Str::ucfirst(str_replace(['-', "_"], ' ', Helper::arrayFirst($routeParts)));
        $this->pageTitle = $this->setPageTitle($this->page, Helper::arrayLast($routeParts));
        $this->formTitle = $this->formTitle ?: $this->pageTitle;
        $this->routePrefix = $this->routePrefix ?: Helper::arrayFirst($routeParts);
        $this->uploadPath = Helper::getUploadPath($this->model);

        $this->formActionId = request()->route('id_or_slug');
        $this->actionMessage = [];
        $this->formActionRoute = $route;
        $this->formActionMethod = (Route::is('*.edit') && $this->formActionId) ? 'PATCH' : 'POST';
        $this->formRequiredItems = $this->model->getRequiredItems();
        $this->formIgnoreItems = $this->model->getIgnoreItems();
        $this->formIgnoreItemsOnUpdate = $this->model->getIgnoreItemsOnUpdate();
        $this->formHasParents = $this->model->getRelationalFields($this->formActionId, get_class($this->model));

        $this->infoItems = $this->model->getInfoItems();
        $this->listColumns = $this->model->getListColumns();

        if (!Route::is('*.create')) {
            $this->prefetchData();
            $this->setListColumns();
            $this->checkPrefetchData($route);
        }

        if (Route::is('*.create') || Route::is('*.edit')) {
            $this->setFormItems();
        }
    }

    public function prefetchData($justQuery = false)
    {
        if ($this->formActionId) {
            $this->data = $this->model->with(array_keys($this->formHasParents))->find($this->formActionId);
        } else {
            $query = $this->model;

            if ($value = request()->input('query')) {
                $query = $query->searchColumns($value);
            }

            if ($value = request()->input('date')) {
                $query = $query->searchAllColumns($value, ["created_at"]);
            }

            if (($startingDay = request()->input('starting-day')) && ($endingDay = request()->input('ending-day'))) {
                $query = $query->inRange('created_at', $startingDay, $endingDay);
            }

            if($justQuery){
                return $query;
            }

            $this->data = $query->paginate($this->itemPerPage);
        }
    }

    public function checkPrefetchData($route = null)
    {
        if ((!$this->data || !$this->formActionId) && preg_match("/\.(view|edit|delete)/i", $route)) {
            // && basename($_SERVER['SCRIPT_NAME']) != 'artisan'
            $route = $this->routePrefix ? "{$this->routePrefix}.index" : env("CRUD_HOME_ROUTE", $this->homeRoute);

            return redirect()->route($route)->send();
        }
    }

    public function setPageTitle(string $title, string $preFix = "", string $subFix = "")
    {
        if (strtolower($preFix) == "index") {
            return trim("List of " . Str::plural($title) . $subFix);
        }

        $preFix = (!preg_match("/{$preFix}/i", $title)) ? ucwords($preFix) : "";
        $subFix = (!preg_match("/{$subFix}/i", $title)) ? ucwords($subFix) : "";

        return trim("{$preFix} {$title} {$subFix}");
    }

    public function setFormItems()
    {
        $this->formItems = $this->data ? $this->data->getFormable() : $this->model->getFormable();
        /*$formItems = [];
        $types     = $this->model->getInputTypes();
        $dataItems = $this->formRequiredItems;

        if (count($dataItems) < 1) {
            $dataItems = $this->model->getFillable();
        }

        foreach ($this->model->getInfoItems() as $item) {
            $title = ucwords(str_replace('_', ' ', $item));

            if (in_array($item, $types['textarea'])) {
                $formItems[$item] = [
                    $title, "textarea", $item, 7 => "disabled",
                ];
            } elseif (in_array($item, $types['select'])) {
                $formItems[$item] = [
                    $title, "select", $item, getStatus(), 7 => "disabled",
                ];
            } else {
                $formItems[$item] = [
                    $title, "text", $item, 7 => "disabled",
                ];
            }
        }

        foreach ($dataItems as $item) {
            if (in_array($item, $this->model->getInfoItems())) {
                continue;
            }

            $title = ucwords(str_replace('_', ' ', $item));

            if (in_array($item, $types['file'])) {
                $formItems[$item] = [
                    $title, "file", $item,
                ];
            } elseif (in_array($item, $types['image'])) {
                $formItems[$item] = [
                    $title, "image", $item,
                ];
            } elseif (in_array($item, $types['video'])) {
                $formItems[$item] = [
                    $title, "video", $item,
                ];
            } elseif (in_array($item, $types['textarea'])) {
                $formItems[$item] = [
                    $title, "textarea", $item,
                ];
            } elseif (in_array($item, $types['select'])) {
                $formItems[$item] = [
                    $title, "select", $item, getStatus(),
                ];
            } else {
                $formItems[$item] = [
                    $title, "text", $item,
                ];
            }
        }

        foreach ($this->formHasParents as $key => $item) {
            $select    = 'select';
            $title     = ucwords(str_replace('_', ' ', $key));
            $joinModel = $this->model->getRelationalModel($key);

            if ($joinModel->getJoinType() == 'manyToMany') {
                $select .= '2';
            } elseif ($joinModel->getJoinType() == 'oneToMany') {
                $key = Str::singular($key) . "_id";
            }

            $formItems[$key] = [$title, $select, $key, $item];
        }*/
    }

    public function setListColumns()
    {
        $listItem = [];
        $data = $this->model->getListColumns();

        if (empty($data)) {
            $data = $this->model->getFillable();
        }

        foreach ($data as $item) {
            $title = str_replace('order_index', 'order', $item);
            $listItem[$item] = str_replace('_', ' ', $title);
        }

        $this->listColumns = $listItem;
    }

    public function redirectToSingleView($url = null)
    {
        if ($url) {
            return redirect()->away($url);
        } else {
            return redirect()->route("{$this->routePrefix}.view", $this->data->id)->with($this->actionMessage)->send();
        }
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $hasAccess
     *
     * @return RedirectResponse|null
     */
    public function hasAccess($hasAccess): ?RedirectResponse
    {
        $message = ['warning' => 'This Action is not permitted !'];
        $query = ["query" => request()->query('query'), "page" => request()->query('page')];

        if (!$hasAccess) {
            return redirect()->route("{$this->routePrefix}.index", $query)->with($message)->send();
        }

        return null;
    }

    public function hasViewAccess(): void
    {
        $this->hasAccess($this->isViewable);
    }

    public function hasCreateAccess(): void
    {
        $this->hasAccess($this->isCreatable);
    }

    public function hasEditAccess(): void
    {
        $this->hasAccess($this->isEditable);
    }

    public function hasDeleteAccess(): void
    {
        $this->hasAccess($this->isDeletable);
    }
}
