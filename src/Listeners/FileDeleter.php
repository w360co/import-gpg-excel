<?php

namespace W360\ImportGpgExcel\Listeners;


use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use W360\ImportGpgExcel\Traits\HasStorage;


class FileDeleter
{

    use HasStorage;

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
            $realPath = $this->getDisk($event->import->storage)->path($event->import->storage . DIRECTORY_SEPARATOR . $event->import->name);
            if (file_exists($realPath)) {
                $ext = pathinfo($realPath, PATHINFO_EXTENSION);
                $filepathOut = str_replace(".$ext", ".$extOut", $realPath);
                @unlink($realPath);
                if (file_exists($filepathOut)) {
                    @unlink($filepathOut);
                }
            }
        }
    }

}