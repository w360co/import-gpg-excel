<?php

namespace W360\ImportGpgExcel\Listeners;


use Illuminate\Support\Facades\Storage;


class FileImporter
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param object $event
     */
    public function handle($event)
    {
        if ($event->import) {
            $extOut = strtolower(config('gnupg.extension_output', 'XLSX'));
            if (class_exists($event->import->model_type)) {
                $realPath = Storage::disk($event->import->storage)->path($event->import->storage . '/' . $event->import->name);
                $ext = pathinfo($realPath, PATHINFO_EXTENSION);
                $filepathOut = str_replace(".$ext", ".$extOut", $realPath);
                if(file_exists($filepathOut)){
                    $ImportModel = new $event->import->model_type($event->import);
                    $ImportModel->import($filepathOut);
                }
            }
        }
    }
}