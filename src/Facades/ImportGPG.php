<?php

namespace W360\ImportGpgExcel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \W360\ImportGpgExcel\Facades\Image
 */
class ImportGPG extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'ImportGPG';
    }
}
