<?php

namespace W360\ImportGpgExcel\Traits;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

Trait HasStorage
{

    /**
     * get storage disk
     *
     * @param string $storage
     * @return Filesystem
     */
    protected function getDisk(string $storage)
    {
        return Storage::disk($this->getDriver($storage));
    }

    /**
     * get driver
     *
     * @param string $storage
     * @return string
     */
    protected function getDriver(string $storage): string{
        $driver = config('gnupg.storage');
        if (config()->has('filesystem.disks.' . $storage)) {
            $driver = $storage;
        }
        return $driver;
    }
}