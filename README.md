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

## Examples
### Example of used to load Excel files encrypted with OpenPGP

####App\Imports\UsersImport.php
```PHP
<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\User;
use W360\ImportGpgExcel\Imports\GpgImport;

class UsersImport extends GpgImport implements ToModel
{

    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new User([
            'name'     => $row[0],
            'email'    => $row[1],
            'password' => Hash::make($row[2]),
        ]);
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
    private function upload(Request $request){
        if($request->hasFile('file')){
            ImportGPG::create($request->file, 'default', UsersImport::class);
        }
    }
    
    public function showImportFiles(){
         return Import::all();
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
