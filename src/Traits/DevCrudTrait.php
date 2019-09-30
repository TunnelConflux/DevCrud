<?php
/**
 * Project      : DevCrud
 * File Name    : DevCrudTrait.php
 * Author       : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/06/29 6:36 PM
 */

namespace TunnelConflux\DevCrud\Traits;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use TunnelConflux\DevCrud\Helpers\DevCrudHelper as Helper;
use TunnelConflux\DevCrud\Models\Enums\JoinTypes;
use TunnelConflux\DevCrud\Requests\SaveFormRequest;
use TunnelConflux\DevCrud\Requests\UpdateFormRequest;

trait DevCrudTrait
{
    public function index()
    {
        if (view()->exists("{$this->viewPrefix}.index")) {
            return view("{$this->viewPrefix}.index", (array)$this);
        }

        return view("dev-crud::index", (array)$this);
    }

    public function create()
    {
        $this->hasCreateAccess();

        if (view()->exists("{$this->viewPrefix}.form")) {
            return view("{$this->viewPrefix}.form", (array)$this);
        }

        return view('dev-crud::form', (array)$this);
    }

    public function store(SaveFormRequest $request)
    {
        $this->hasCreateAccess();

        $files = $this->saveFiles($request);
        $inputs = $request->input();

        foreach ($inputs as $key => $val) {
            $inputs[$key] = $val ?: (($val === 0) ? 0 : null);
        }

        $this->data = $this->model->create($inputs);

        if ($this->data) {
            $this->saveRelationalData($request);
            $this->actionMessage = ['success' => 'Item Added Successfully !'];
        } else {
            $this->actionMessage = ['error' => 'Item Added Failed !'];

            foreach ($files as $file) {
                @unlink($this->uploadPath . '/' . $file);
            }
        }

        if ($this->redirectAfterAction) {
            $this->redirectToSingleView();
        }
    }

    public function show()
    {
        $this->hasViewAccess();

        if (view()->exists("{$this->viewPrefix}.show")) {
            return view("{$this->viewPrefix}.show", (array)$this);
        }

        return view('dev-crud::show', (array)$this);
    }

    public function edit()
    {
        $this->hasEditAccess();

        if ($this->data->password ?? null) {
            $this->data->password = "";
        }

        if (view()->exists("{$this->viewPrefix}.form")) {
            return view("{$this->viewPrefix}.form", (array)$this);
        }

        return view('dev-crud::form', (array)$this);
    }

    public function update(UpdateFormRequest $request)
    {
        $this->hasEditAccess();

        $files = $this->saveFiles($request);
        $inputs = $request->input();

        foreach ($inputs as $key => $val) {
            $inputs[$key] = $val ?: (($val === 0) ? 0 : null);
        }

        if ($this->data->update($inputs)) {
            $this->saveRelationalData($request);
            $this->actionMessage = ['success' => 'Item updated successfully !'];
        } else {
            $this->actionMessage = ['error' => 'Item updating Failed !'];

            foreach ($files as $file) {
                @unlink($this->uploadPath . '/' . $file);
            }
        }

        if ($this->redirectAfterAction) {
            $this->redirectToSingleView();
        }
    }

    public function destroy()
    {
        $this->hasDeleteAccess();

        if ($this->data) {
            $images = [];

            foreach ($this->model->getInputTypes() as $key => $item) {
                if (in_array($key, ['file', 'image', 'video'])) {
                    $images[] = $item;
                }
            }

            if ($this->data->delete()) {
                foreach ($images as $image) {
                    @unlink($this->uploadPath . '/' . $this->data->{$image});
                }

                $this->actionMessage = ['success' => 'Item Deleted successfully!'];
            } else {
                $this->actionMessage = ['error' => 'Item Deleting Failed !'];
            }
        }

        return redirect()->route($this->routePrefix . '.index',
            request()->only(['page', 'query']))->with($this->actionMessage);
    }

    public function combinePivot($entities, $pivots = [])
    {
        // Set array
        $pivotArray = [];
        // Loop through all pivot attributes
        foreach ($pivots as $pivot => $value) {
            // Combine them to pivot array
            $pivotArray += [$pivot => $value];
        }
        // Get the total of arrays we need to fill
        $total = count($entities);
        // Make filler array
        $filler = array_fill(0, $total, $pivotArray);

        // Combine and return filler pivot array with data
        return array_combine($entities, $filler);
    }

    /**
     * Save files from request & assign as input
     *
     * @param FormRequest $request
     *
     * @return array
     */
    public function saveFiles(FormRequest $request): array
    {
        $files = [];

        foreach ($this->formItems as $key => $item) {
            if ($request->file($key)) {
                if ($item->type == 'image') {
                    $files[$key] = Helper::saveFile($request->file($key), $this->uploadPath);
                } elseif ($item->type == 'file') {
                    $files[$key] = Helper::saveFile($request->file($key), $this->uploadPath);
                } elseif ($item->type == 'video') {
                    $files[$key] = Helper::saveFile($request->file($key), $this->uploadPath);
                }
            }
        }

        $request->request->add($files);

        return $files;
    }

    public function saveRelationalData(FormRequest $request)
    {
        try {
            foreach ($this->formHasParents as $key => $val) {
                /**
                 * @var \TunnelConflux\DevCrud\Models\JoinModel
                 */
                $joinModel = $this->model->getRelationalModel($key);

                if ($joinModel->getJoinType() == JoinTypes::ManyToMany) {
                    if (empty($joinModel->getPivotExtra())) {
                        $this->data->{$key}()->sync($request->input($key));
                    } else {
                        foreach ($joinModel->getPivotExtra() as $item) {
                            $this->data->{$key}()->sync($request->input($item));
                        }
                    }
                } elseif ($joinModel->getJoinType() == JoinTypes::OneToMany) {
                    $key = Str::snake(Str::singular($key));
                    $this->data->{$key . "_id"} = $request->input($key . "_id") ?? null;
                    $this->data->save();
                }
            }
        } catch (Exception $e) {
            Log::error("CRUD::CREATE_OR_UPDATE_ERROR, error: {$e->getMessage()}");
        }
    }

    public function getValidationRules(): array
    {
        return [];
    }

    public function getValidationMessages(): array
    {
        return [];
    }
}
