<?php

namespace W360\ImportGpgExcel;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use W360\ImportGpgExcel\Events\Decrypting;
use W360\ImportGpgExcel\Events\Importing;
use W360\ImportGpgExcel\Listeners\FileDecrypter;
use W360\ImportGpgExcel\Listeners\FileImporter;


class EventServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected $listen = [
        Decrypting::class => [
            FileDecrypter::class
        ],
        Importing::class => [
            FileImporter::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

}