<?php

/*
 * You can place your custom package configuration in here.
 */

use Maatwebsite\Excel\Excel;

return [


    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'storage' => 'local',

    /*
    |--------------------------------------------------------------------------
    | GPG Secret Passphrase
    |--------------------------------------------------------------------------
    |
    |  passphrase to decrypt uploaded gpg files
    |
    */

    'secret_passphrase' => env('GPG_SECRET_PASSPHRASE', 'default'),


    /*
    |--------------------------------------------------------------------------
    | GPG Extension Output
    |--------------------------------------------------------------------------
    |
    |  extension to save the decrypted gpg file
    |
    */

    'extension_output' => env('GPG_EXTENSION_OUTPUT', Excel::XLSX),


    /*
     |--------------------------------------------------------------------------
     | GPG Signing Key
     |--------------------------------------------------------------------------
     |
     |  extension to save the decry
     |
     */

    'private_key' => env('GPG_PRIVATE_KEY', __DIR__.'/../key.asc')



];