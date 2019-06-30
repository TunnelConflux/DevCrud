<?php
/**
 * Project      : DevCrud
 * File Name    : DevCrudController.php
 * User         : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/06/26 6:21 PM
 */

namespace TunnelConflux\DevCrud\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use TunnelConflux\DevCrud\Http\Controllers\Traits\CrudAble;
use TunnelConflux\DevCrud\Http\Traits\DevCrudTrait;
use TunnelConflux\DevCrud\Models\DevCrudModel;

class DevCrudController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use DevCrudTrait;

    public $data;
    public $page;
    public $pageTitle;
    public $routePrefix;
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
    public $formUpdateIgnoreItems;
    public $formHasParents;
    public $paginate;
    public $uploadPath;
    public $actionMessage;
    public $indexListColumns;

    public $indexNewAction;
    public $indexViewAction;
    public $indexEditAction;
    public $indexDeleteAction;
    public $indexItemPerPage = 15;

    protected $homeRoute           = "dashboard";
    protected $redirectAfterAction = true;

    /**
     * @var \TunnelConflux\DevCrud\Models\DevCrudModel
     */
    protected $model;
    protected $viewPrefix;

    public function __construct(DevCrudModel $model)
    {
        /*if (request()->route('id_or_slug')) {
            $this->formActionId = request()->route('id_or_slug');
        }

        $this->model       = $model;
        $pageTag           = explode('.', Route::currentRouteName());
        $this->page        = trim(ucwords(str_replace('-', ' ', $pageTag[0])));
        $this->pageTitle   = ((ucwords(end($pageTag)) != 'Index' && count($pageTag) > 1) ? ucwords(end($pageTag)) . ' ' : '') . $this->page;
        $this->formTitle   = $this->pageTitle;
        $this->routePrefix = $pageTag[0];
        $this->uploadPath  = getUploadPath($this->model);

        $this->actionMessage         = [];
        $this->formActionRoute       = Route::currentRouteName();
        $this->formActionMethod      = (Route::is('*.edit') && $this->formActionId) ? 'PATCH' : 'POST';
        $this->formRequiredItems     = $this->model->getRequiredItems();
        $this->formIgnoreItems       = $this->model->getIgnoreItems();
        $this->formUpdateIgnoreItems = $this->model->getIgnoreItemsOnUpdate();
        $this->formHasParents        = $this->model->getRelationalFields(request()->route('id_or_slug'), get_class($this->model));

        $this->indexListColumns  = $this->model->getListColumns();
        $this->indexNewAction    = true;
        $this->indexViewAction   = true;
        $this->indexEditAction   = true;
        $this->indexDeleteAction = true;

        if (!Route::is('*.create')) {
            $this->setData();
        }

        //if (Route::is('*.create') || Route::is('*.edit') || Route::is('*.index') || Route::is('*.view')) {
        $this->setFormItems();
        //}*/
    }

    public function setData()
    {
        if ($this->formActionId) {
            $this->data = $this->model->with(array_keys($this->formHasParents))->find($this->formActionId);
        } else {
            $query = $this->model;

            if ($value = request()->input('query')) {
                $query = $query->searchAllColumns($value);
            }

            if ($value = request()->input('date')) {
                $query = $query->searchAllColumns($value, ["created_at"]);
            }

            $this->data = $query->paginate($this->indexItemPerPage);
            $this->setIndexListColumns();
        }

        $this->checkData();
    }

    public function setIndexListColumns()
    {
        $listItem = [];
        $data     = $this->model->getIndexListColumns();

        if (empty($data)) {
            $data = $this->model->getFillable();
        }

        foreach ($data as $item) {
            $title           = str_replace('order_index', 'order', $item);
            $listItem[$item] = str_replace('_', ' ', $title);
        }

        $this->indexListColumns = $listItem;
    }

    public function checkData()
    {
        $pageTag = explode('.', Route::currentRouteName());

        if ((!$this->data || !$this->formActionId) && in_array(strtolower(end($pageTag)), ["view", "edit", "delete"]) &&
            basename($_SERVER['SCRIPT_NAME']) != 'artisan'
        ) {
            $route = $this->routePrefix ? "{$this->routePrefix}.index" : env("CRUD_HOME_ROUTE", $this->homeRoute);

            return redirect()->route(($route))->send();
        }
    }

    public function setFormItems()
    {
        $formItems = [];
        $types     = $this->model->getFormInputTypes();
        $dataItems = $this->formRequiredItems;

        if (count($dataItems) < 1) {
            $dataItems = $this->model->getFillable();
        }

        foreach ($this->model->getFormInfoItems() as $item) {
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

            if (in_array($item, $this->model->getFormInfoItems())) {
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
            $joinModel = $this->model->getFormRelationalModel($key);

            if ($joinModel->getJoinType() == 'manyToMany') {
                $select .= '2';
            } elseif ($joinModel->getJoinType() == 'oneToMany') {
                $key = Str::singular($key) . "_id";
            }

            $formItems[$key] = [$title, $select, $key, $item];
        }

        $this->formItems = $formItems;
    }

    public function getValidationRules()
    {
        $fields = [];

        if (!$this->model instanceof DevCrudModel) {
            return $fields;
        }

        if (count($this->formRequiredItems) > 0) {
            foreach ($this->formRequiredItems as $field) {
                $fields[$field] = !in_array($field, $this->formIgnoreItems) ? ['required'] : ['nullable'];
            }
        } else {
            foreach ($this->model->getFillable() as $field) {
                $fields[$field] = !in_array($field, $this->formIgnoreItems) ? ['required'] : ['nullable'];
            }
        }

        if (Route::is('*.edit') && count($this->formUpdateIgnoreItems) > 0) {
            foreach ($fields as $key => $val) {
                if (in_array($key, $this->formUpdateIgnoreItems)) {
                    try {
                        if (is_string($fields[$key])) {
                            $fields[$key] = str_replace("required", "nullable", $fields[$key]);
                        } elseif (is_array($fields[$key]) && count((array)$fields[$key]) > 0) {
                            $fields[$key] = array_map(function ($v) {
                                return ($v == "required") ? "nullable" : $v;
                            }, $fields[$key]);
                        } else {
                            $fields[$key] = ["nullable"];
                        }
                    } catch (Exception $e) {

                    }
                }
            }
        }

        return $fields;
    }

    public function getValidationMessages()
    {
        return [];
    }

    public function redirectToSingleView($url = null)
    {
        if ($url) {
            return redirect()->away($url);
        } else {
            return redirect()->route("{$this->routePrefix}.view", $this->data->id)->with($this->actionMessage)->send();
        }
    }

    public function checkActionStatus($actionStatus)
    {
        $message = ['warning' => 'This Action disable !'];
        $query   = ["query" => request()->query('query'), "page" => request()->query('page')];

        if (!$actionStatus) {
            return redirect()->route("{$this->routePrefix}.index", $query)->with($message)->send();
        }
    }

    public function checkNewActionStatus()
    {
        $this->checkActionStatus($this->indexNewAction);
    }

    public function checkEditActionStatus()
    {
        $this->checkActionStatus($this->indexEditAction);
    }

    public function checkDeleteActionStatus()
    {
        $this->checkActionStatus($this->indexDeleteAction);
    }
}