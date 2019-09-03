<?php
/**
 * Project      : DevCrud
 * File Name    : DevCrudHelper.php
 * Author       : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/07/14 6:43 PM
 */

namespace TunnelConflux\DevCrud\Helpers;

use Collective\Html\FormFacade as Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Intervention\Image\Facades\Image;
use TunnelConflux\DevCrud\Models\Formable;

class DevCrudHelper
{
    use Macroable;

    /*************************
     **  String Functions   **
     *************************/

    /**
     * Explode String & remove empty elements
     *
     * @param        $string
     * @param string $delimiter
     *
     * @return array
     */
    static function explodeString($string, $delimiter = ','): array
    {
        $string = trim($string);
        $result = [];

        if ($string) {
            $result = static::arrayTrim(explode($delimiter, $string));
        }

        return $result;
    }

    /**
     * Get Upload path according to model
     *
     * @param Model  $modelName
     * @param string $file
     * @param string $parent
     *
     * @return string
     */
    static function getUploadPath(Model $modelName = null, $file = null, $parent = "uploads"): string
    {
        if (!$modelName instanceof Model) {
            return public_path($parent);
        }

        $modelName = class_basename($modelName);
        $directoryName = public_path('uploads');
        $parts = static::explodeString(Str::snake($modelName), '_');

        foreach ($parts as $part) {
            if ($part) {
                $directoryName .= "/" . Str::plural($part);
            }
        }

        return $file ? "{$directoryName}/{$file}" : $directoryName;
    }

    /**
     * Generate slug from string
     *
     * @param Model  $model
     * @param string $string
     * @param string $separator
     * @param string $dbKey
     *
     * @return string
     */
    static function makeSlug(Model $model, $string, $separator = "-", $dbKey = "slug"): string
    {
        $slugRows = [];
        $slug = Str::slug(trim($string));

        if (!$slug || ((int)$slug + 0) > 0) {
            $char = ["{", "}", "(", ")", ",", "\\", "^", "[", "]", "<", ">", "`", '"', "'", ";", "/", "|", "?", "!", ":", "%", "@", "#", "&", "=", "+", "$", ","];
            $slug = strtolower(preg_replace('/\s+/u', $separator, $string));
            $slug = str_replace($char, '', $slug);
        }

        $slug = trim($slug, '-');
        $sql = $model::select($dbKey)->whereRaw("{$dbKey} REGEXP '^{$slug}(-[0-9]*)?$'");

        if (isset($model->id)) {
            $slugs = $sql->where('id', '!=', $model->id)->pluck($dbKey);

            if (!in_array($slug, $slugs->toArray())) {
                return $slug;
            }
        } else {
            $slugs = $sql->orderBy($dbKey)->pluck($dbKey);
        }

        if (count($slugs) > 0) {
            $parts = explode('-', str_replace($slug, '', $slugs->last()));
            $countSlug = ((int)last($parts) ?: 1) + 1;

            return "{$slug}-{$countSlug}";
        }

        return $slug;
    }

    static function generateHexId($number, $prefix = "", $length = 8)
    {
        mt_srand($number);
        $r = mt_rand();

        if ($prefix == 'date') {
            $prefix = substr(date('M'), 0, 1) . substr(date('D'), 0, 1);
        } elseif ($prefix == 'month') {
            $prefix = substr(date('M'), 0, 3);
        }

        return substr(strtoupper($prefix) . sprintf("%0{$length}X", $r), 0, $length);
    }

    static function fixJson($json)
    {
        $regex =
            <<<'REGEX'
~ "[^"\\]*(?:\\.|[^"\\]*)*" (*SKIP)(*F) | '([^'\\]*(?:\\.|[^'\\]*)*)' ~x
REGEX;

        return preg_replace_callback($regex, function ($matches) {
            return '"' . preg_replace('~\\\\.(*SKIP)(*F)|"~', '\\"', $matches[1]) . '"';
        }, $json);
    }

    static function convertJson($string, $array = false)
    {
        if (is_string($string)) {
            $string = self::fixJson($string);

            return json_decode($string, $array) ?: ($array ? [] : null);
        } elseif (is_array($string)) {
            return json_decode(json_encode($string), $array) ?: ($array ? [] : null);
        } elseif (is_object($string)) {
            if (preg_match("/Stream$/i", get_class($string))) {
                return json_decode($string, $array) ?: ($array ? [] : null);
            } else {
                return json_decode(json_encode($string), $array) ?: ($array ? [] : null);
            }
        }

        return ($array ? [] : null);
    }

    static function makeJson($data, int $options = 0, int $depth = 512)
    {
        return json_encode($data, $options, $depth);
    }


    /************************
     **  Array Functions   **
     ************************/

    /**
     * Remove empty elements from an Array
     *
     * @param array $data
     * @param bool  $specialChar
     *
     * @return array
     */
    static function arrayTrim(array $data, $specialChar = false): array
    {
        $result = [];

        foreach ($data as $key => $item) {
            if (is_array($item)) {
                $result[$key] = static::arrayTrim($item, $specialChar);
            } else if (trim($item) != "") {
                $result[$key] = trim($item);

                if ($specialChar) {
                    $result[$key] = preg_replace('/^\W*(.*?)\W*$/', '', $result[$key]);
                }
            }
        }

        return $result;
    }

    static function arrayFirst(array $array)
    {
        foreach ($array as $item) {
            return $item;
        }

        return null;
    }

    static function arrayLast(array $array)
    {
        return end($array);
    }

    static function arrayPush(&$array, ...$elements)
    {
        return static::arrayMerge($array, $elements);
    }

    static function arrayMerge(&$array, array $elements, $atFirst = false)
    {
        if ($atFirst) {
            return $array = $elements + $array;
        }

        return $array = array_merge($array, $elements);
    }


    /*************************
     **   Route Functions   **
     *************************/

    /**
     * Set Routes for Crud
     *
     * @param string $name
     * @param string $controller
     * @param string $delete
     * @param string $update
     *
     * @return void
     */
    static function setRoutes($name, $controller, $delete = 'delete', $update = 'patch'): void
    {
        $controller .= "@";
        $routeName = explode('/', $name)[0];
        $pluralName = Str::plural($routeName);

        app("router")
            ->prefix($pluralName)
            ->name("$routeName.")
            ->group(function ($router) use ($controller, $delete, $update) {
                $router->get('/', "{$controller}index")->name('index');
            });

        app("router")
            ->prefix($name)
            ->name("$routeName.")
            ->group(function ($router) use ($controller, $delete, $update) {
                $router->get('/new', "{$controller}create")->name('create');
                $router->post('/new', "{$controller}store")->name('create');
                $router->get('/{id_or_slug}/edit', "{$controller}edit")->name('edit');
                $router->{$update}('/{id_or_slug}/edit', "{$controller}update")->name('edit');
                $router->get('/{id_or_slug}/view', "{$controller}show")->name('view');
                $router->{$delete}('/{id_or_slug}/delete', "{$controller}destroy")->name('delete');
            });
    }


    /************************
     **   Form Functions   **
     ************************/

    /**
     * @param Formable $item
     * @param object   $data
     *
     * @return string
     */
    static function getInputField(Formable $item, $data)
    {
        $editorOption = "<a href='javascript:void(0)' id='enable-editor' class='btn btn-default btn-sm margin-r-5'>Enable Editor</a>" .
                        "<a href='javascript:void(0)' id='disable-editor' class='btn btn-default btn-sm'>Disable Editor</a>";
        $title = $item->title;
        $script = "";
        $item->disable = $item->disable ? "disabled" : "";
        $item->multiple = $item->multiple ? "multiple" : "";

        if ($item->class == 'froala-editor' && $item->type == 'textarea') {
            $title .= " | {$editorOption}";
            $script = "
            <script type='text/javascript'>
                window.onload = function () {
                    // Destroy action.
                    $('a#disable-editor').on('click', function (e) {
                        e.preventDefault();
                        if ($('.froala-editor').data('froala.editor')) {
                            $('.froala-editor').froalaEditor('destroy');
                        }
                    });
        
                    // Initialize action.
                    $('a#enable-editor').on('click', function (e) {
                        e.preventDefault();
                        if (!$('.froala-editor').data('froala.editor')) {
                            $('.froala-editor').froalaEditor();
                        }
                    });
                };
            </script>
        ";
        }

        $pre = "<div class=\"form-group\" id=\"group-{$item->name}\"><label for=\"{$item->name}\">{$title}</label>{$script}";
        $field = "";
        $post = "</div>";
        $placeholder = explode('| <', $item->title)[0];

        if (in_array($item->type, ['file', 'image', 'video'])) {
            $field = "<div class=\"input-group\">
                      <div class=\"custom-file\">
                        <input type=\"file\" class=\"custom-file-input\" id=\"{$item->name}\" name=\"{$item->name}\">
                        <label class=\"custom-file-label\" for=\"{$item->name}\">" . ($item->placeholder ?: "Choose file") . "</label>
                      </div>
                      
                    </div>";
        } elseif ($item->type == 'select') {
            $field = Form::{$item->type}($item->multiple ? "{$item->name}[]" : $item->name, $item->options, old($item->name) ?: $item->value, ['class' => 'form-control ' . $item->class, 'id' => $item->name, $item->multiple, $item->disable]);
        } elseif ($item->type == 'date') {
            $field = Form::{$item->type}($item->name, old($item->name) ?: @$data->{$item->name}, ['class' => 'form-control ' . $item->class, 'id' => $item->name, 'placeholder' => $item->placeholder ?: "Select {$placeholder}", $item->multiple, $item->disable]);
        } elseif ($item->type == 'datetime-local') {
            $date = @$data->{$item->name} ? date('Y-m-d\TH:i:s', strtotime($data->{$item->name})) : null;
            $field = '<input  value="' . $date . '" class="form-control " id="' . $item->name . '" placeholder="' . ($item->placeholder ?: "Select " . $item->title) . '" name="' . $item->name . '" type="' . $item->type . '"' . $item->disable . '>';
        } else {
            $field = Form::{$item->type}($item->name, old($item->name) ?: @$data->{$item->name}, ['class' => 'form-control ' . $item->class, 'id' => $item->name, 'placeholder' => $item->placeholder ?: $placeholder, $item->multiple, $item->disable]);
        }

        if ($item->type == 'hidden') {
            return "<input id='{$item->name}' name='{$item->name}' value='" . (old($item->name) ?: @$data->{$item->name}) . "' type='hidden' >";
        } else {
            return $pre . $field . $post;
        }
    }

    /**
     * @param int|null $code
     *
     * @return array|string
     */
    static function getStatus($code = null)
    {
        $data = [
            '0' => 'Inactive',
            '1' => 'Active',
        ];

        if (is_integer($code) || is_string($code)) {
            return $data[$code] ?? "{$code}";
        } else {
            return $data;
        }
    }

    /**
     * @param int|null $code
     *
     * @return array|string
     */
    static function getYesNo($code = null)
    {
        $data = [
            '0' => 'No',
            '1' => 'Yes',
        ];

        if (is_integer($code) || is_string($code)) {
            return $data[$code] ?? "{$code}";
        } else {
            return $data;
        }
    }


    /********************************
     **   File & Image Functions   **
     ********************************/

    /**
     * @param UploadedFile $file
     * @param string|null  $uploadPath
     * @param int|null     $width
     * @param int|null     $height
     * @param int|null     $compress
     *
     * @return bool|string
     */
    static function saveFile(UploadedFile $file, $uploadPath = null, $width = null, $height = null, $compress = 85)
    {
        if (!$file) {
            return false;
        }

        $year = date('Y');
        $month = date('m');
        $mime = str_replace(['image/', 'jpeg'], ['', 'jpg'], $file->getClientMimeType());
        $name = Str::uuid() . '.' . $mime;
        $fileSave = false;

        if (!$uploadPath) {
            $genericPath = "uploads/{$year}/{$month}";
        } else {
            $genericPath = "{$uploadPath}/{$year}/{$month}";
        }

        if (!file_exists($genericPath)) {
            @mkdir($genericPath, 0755, true);
        }

        if (!in_array($mime, ['jpg', 'png'])) {
            $fileMime = last(explode('.', $file->getClientOriginalName()));
            $fileName = explode('.', $name);

            if ($file->move($genericPath, "{$fileName[0]}.{$fileMime}")) {
                $fileSave = "{$fileName[0]}.{$fileMime}";
            }
        } else {
            $img = Image::make($file);

            if ($width || $height) {
                $img->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            if ($img->save("{$genericPath}/{$name}", $compress)) {
                $fileSave = $name;
            }
        }

        $prePath = $uploadPath ? "{$year}/{$month}" : $genericPath;

        return $fileSave ? trim("{$prePath}/{$fileSave}", '/') : $fileSave;
    }

    static function getFileUrl($imagePath, $image)
    {
        if (!$image) {
            return null;
        }

        if ($imagePath instanceof Model) {
            $imagePath = self::getUploadPath($imagePath);
        }

        return asset(str_replace(public_path(), '', $imagePath . DIRECTORY_SEPARATOR . $image));
    }


    /************************
     **   Http Functions   **
     ************************/

    /**
     * @param int $code
     *
     * @return array|mixed
     */
    static function getHttpStatusCodeNames($code = null)
    {
        $codes = [
            '100' => 'Continue',
            '101' => 'Switching Protocols',
            '102' => 'Processing',
            '200' => 'OK',
            '201' => 'Created',
            '202' => 'Accepted',
            '203' => 'Non-authoritative Information',
            '204' => 'No Content',
            '205' => 'Reset Content',
            '206' => 'Partial Content',
            '207' => 'Multi-Status',
            '208' => 'Already Reported',
            '226' => 'IM Used',
            '300' => 'Multiple Choices',
            '301' => 'Moved Permanently',
            '302' => 'Found',
            '303' => 'See Other',
            '304' => 'Not Modified',
            '305' => 'Use Proxy',
            '307' => 'Temporary Redirect',
            '308' => 'Permanent Redirect',
            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '402' => 'Payment Required',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '405' => 'Method Not Allowed',
            '406' => 'Not Acceptable',
            '407' => 'Proxy Authentication Required',
            '408' => 'Request Timeout',
            '409' => 'Conflict',
            '410' => 'Gone',
            '411' => 'Length Required',
            '412' => 'Max plan limit reached/Precondition Failed',
            '413' => 'Payload Too Large',
            '414' => 'Request-URI Too Long',
            '415' => 'Unsupported Media Type',
            '416' => 'Requested Range Not Satisfiable',
            '417' => 'Expectation Failed',
            '418' => 'I\'m a teapot',
            '421' => 'Misdirected Request',
            '422' => 'Unprocessable Entity',
            '423' => 'Locked',
            '424' => 'Failed Dependency',
            '426' => 'Upgrade Required',
            '428' => 'Precondition Required',
            '429' => 'Too Many Requests',
            '431' => 'Request Header Fields Too Large',
            '444' => 'Connection Closed Without Response',
            '451' => 'Unavailable For Legal Reasons',
            '499' => 'Client Closed Request',
            '500' => 'Internal Server Error',
            '501' => 'Not Implemented',
            '502' => 'Bad Gateway',
            '503' => 'Service Unavailable',
            '504' => 'Gateway Timeout',
            '505' => 'HTTP Version Not Supported',
            '506' => 'Variant Also Negotiates',
            '507' => 'Insufficient Storage',
            '508' => 'Loop Detected',
            '510' => 'Not Extended',
            '511' => 'Network Authentication Required',
            '599' => 'Network Connect Timeout Error',
        ];

        if ($code) {
            return $codes[$code] ?? $code;
        }

        return $codes;
    }
}
