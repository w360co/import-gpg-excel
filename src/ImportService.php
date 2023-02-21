<?php

namespace W360\ImportGpgExcel;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use W360\ImportGpgExcel\Models\Import;

/**
 * @class ImageService
 * @author Elbert Tous <elbertjose@hotmail.com>
 * @version 2.2.0
 */
class ImportService
{


    /**
     * @param UploadedFile $file
     * @param $storage
     * @param \Closure $function
     * @return mixed
     */
    private function upload(UploadedFile $file, $storage, \Closure $function)
    {
        $disk = Storage::disk($storage);

        $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

        $disk->put($storage . "/" . $fileName, $file->getRealPath());

        return $function($fileName, $storage);
    }


    /**
     * @param UploadedFile $file
     * @param $storage
     * @param $model
     * @return mixed
     */
    public function create(UploadedFile $file, $storage, $model)
    {
        return $this->upload($file, $storage, function($fileName, $storage) use ($model) {
            return Import::firstOrCreate([
                'name' => $fileName,
                'storage' => $storage,
                'total_rows' => 0,
                'failed_rows' => 0,
                'completed_rows' => 0,
                'state' => 'pending',
                'report' => 'log-'.$fileName,
                'author' => (Auth::check() ? Auth::user()->username : null),
                'model_type' => $model
            ]);
        });
    }


    /**
     * @param $storage
     * @param $name
     * @return bool
     */
    public function delete($storage, $name)
    {
        if(isset($storage) && isset($name)) {
            $file = Import::where('storage', $storage)
                ->where('name', $name)
                ->first();
            if($file) {
                if ($file->delete()) {
                    $disk = Storage::disk($storage);
                    return $disk->delete($storage . "/" . $name);
                }
            }
        }
        return true;
    }

}
