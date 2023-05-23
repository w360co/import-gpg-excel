# W360 Import GPG Excel

library to import files encrypted with pgp format

[![runtest](https://github.com/w360co/import-gpg-excel/actions/workflows/laravel-test.yml/badge.svg?branch=main)](https://github.com/w360co/import-gpg-excel/actions/workflows/laravel-test.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/w360/import-gpg-excel)](https://packagist.org/packages/w360/import-gpg-excel)
[![Latest Stable Version](https://img.shields.io/packagist/v/w360/import-gpg-excel)](https://packagist.org/packages/w360/import-gpg-excel)
[![License](https://img.shields.io/packagist/l/w360/import-gpg-excel)](https://packagist.org/packages/w360/import-gpg-excel)

# Table of Contents
<!-- TOC -->
- [Features](#Features)
- [License](#License)
<!-- /TOC -->

## Installation

    > composer require w360/import-gpg-excel

## Publishable

    php artisan vendor:publish --tag=config
    php artisan vendor:publish --tag=migration

## Migration
    
    > php artisan migrate

## Configure
instruct your application to use the database driver by updating the QUEUE_CONNECTION variable in your application's .env file:

    QUEUE_CONNECTION=database
    GPG_SECRET_PASSPHRASE=mysecretpassphrase

finally, instruct your application the path of the GPG private key by updating the GPG_PRIVATE_KEY variable in the .env file or by modifying the gnupg.php file found in your application's config folder:
    
    #.env
    GPG_PRIVATE_KEY=/home/user/.gnupg/private.asc

or

    #/config/gnupg.php
    /*
     |--------------------------------------------------------------------------
     | GPG Signing Key
     |--------------------------------------------------------------------------
     |
     |  extension to save the decry
     |
     */

    'private_key' => env('GPG_PRIVATE_KEY', __DIR__.'/key.asc')

## Examples
### Example of used to load Excel files encrypted with OpenPGP

####App\Imports\UsersImport.php

```PHP
<?php

namespace App\Imports;

use Illuminate\Support\Collection;use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\User;
use W360\ImportGpgExcel\Imports\GpgImport;

class UsersImport extends GpgImport
{

    /**
     * if the object is returned, the row is considered 
     * to have been imported successfully, 
     * otherwise the row will be marked as failed in the report
     * 
     * @param array | Collection $row
     * @return mixed
     * @throws Exception
     */
    public function row($row)
    {
         $findUser = User::where('identifier', $row['identifier'])->first();
         if(!$findUser){
             return User::create([
                'name'     => $row['name'],
                'email'    => $row['email'],
                'password' => Hash::make($row['password']),
            ]);
        }
        return $this->exception('User '.$row['identifier'].' already exists');
    }

}
```
####App\App\Http\Controllers\TestController.php
```PHP
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use W360\ImportGpgExcel\Facades\ImportGPG;
use W360\ImportGpgExcel\Models\Import;
use App\Imports\UsersImport;

class TestController extends Controller
{
    /**
     * allows you to upload a file and associate
     * it with an import file that will be 
     * executed asynchronously via cron jobs
     * 
     * @param Request $request
     */
     private function upload(Request $request){
        if($request->hasFile('file')){
            ImportGPG::create($request->file, 'default', UsersImport::class);
        }
     }
    
    /**
     * show the detail of all imported files
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
     public function showImportFiles(){
         return Import::all();
     }
    
    /**
     * show current import file details
     * 
     * @return mixed
     */
     public function showCurrentImport(){
         return Import::where('model_type', UsersImport::class)
         ->where('state', 'processing')
         ->first();
     }
    
}
```
## Features

- Allows uploading Excel files encrypted with OpenPGP

## Libraries

- Laravel Excel https://docs.laravel-excel.com/

##  License

The MIT License (MIT)

Copyright (c) 2023 W360 S.A.S

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
