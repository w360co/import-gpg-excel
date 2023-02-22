<?php

namespace W360\ImportGpgExcel;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use W360\ImportGpgExcel\Events\Decrypting;
use W360\ImportGpgExcel\Events\Importing;
use W360\ImportGpgExcel\Models\Import;

/**
 * @class ImageService
 * @author Elbert Tous <elbertjose@hotmail.com>
 * @version 2.2.0
 */
class ImportService
{


    /**
     * @param Illuminate\Http\UploadedFile | Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param $storage
     * @param \Closure $function
     * @return mixed
     */
    private function upload($file, $storage, \Closure $function)
    {
        $disk = Storage::disk($storage);
        $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
        $disk->put($storage . "/" . $fileName, file_get_contents($file));
        $import = $function($fileName, $storage);
        if($import) {
            event(new Decrypting($import));
        }
        return $import;
    }


    /**
     * @param Illuminate\Http\UploadedFile | Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param $storage
     * @param $model
     * @return mixed
     */
    public function create($file, $storage, string $model)
    {
        return $this->upload($file, $storage, function($fileName, $storage) use ($model) {
            return Import::firstOrCreate([
                'name' => $fileName,
                'storage' => $storage,
                'total_rows' => 0,
                'failed_rows' => 0,
                'processed_rows' => 0,
                'state' => 'pending',
                'report' => 'log-'.$fileName,
                'author' => (Auth::check() ? Auth::user()->username : null),
                'model_type' => $model
            ]);
        });
    }


}
