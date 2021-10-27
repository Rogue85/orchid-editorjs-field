<?php

namespace AlexSabur\OrchidEditorJSField\Support\Actions;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Orchid\Attachment\File;
use Illuminate\Support\Str;

class LoadImage
{
    static public $loadByUrlCallback;

    static public $loadByRequestCallback;

    public static function loadByUrlUsing($callback)
    {
        # code...
    }

    public static function loadByRequestUsing($callback)
    {
        # code...
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function loadByUrl(Request $request)
    {
        if (static::$loadByUrlCallback) {
            return call_user_func(static::$loadByUrlCallback, $request);
        }

        $stream = @fopen($request->url, 'r');

        $temporaryFile = tempnam(sys_get_temp_dir(), 'editorjs-image');
        file_put_contents($temporaryFile, $stream);

        $filename = basename(parse_url($request->url, PHP_URL_PATH));
        $filename = str_replace('%20', ' ', $filename);

        if ($filename === '') {
            $filename = 'file';
        }

        $mediaExtension = explode('/', mime_content_type($temporaryFile));

        if (!Str::contains($filename, '.')) {
            $filename = "{$filename}.{$mediaExtension[1]}";
        }


        $file = new UploadedFile($temporaryFile, $filename);

        $model = $this->createModel($file, $request);

        return [
            'url' => $model->url
        ];
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function loadByRequest(Request $request)
    {
        if (static::$loadByRequestCallback) {
            return call_user_func(static::$loadByRequestCallback, $request);
        }

        $image = $request->file('image');

        $model = $this->createModel($image, $request);

        return [
            'url' => $model->url
        ];
    }

    protected function createModel(UploadedFile $file, Request $request)
    {
        $model = app()->make(File::class, [
            'file'  => $file,
            'disk'  => $request->get('storage'),
            'group' => $request->get('group'),
        ])->load();

        $model->url = $model->url();

        return $model;
    }
}
