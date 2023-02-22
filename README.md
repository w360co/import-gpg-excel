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
- Example of use uploading a profile photo for a user

```PHP
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use W360\ImportGpgExcel\Facades\ImportGPG;
use W360\ImportGpgExcel\Imports\UsersImport;
use W360\ImportGpgExcel\Models\Import;

class TestController extends Controller
{
    private function saveProfile(Request $request){
        if($request->hasFile('file_pgp') and Auth::check()){
            $storage = 'files';
            $file = $request->file_pgp;
            ImportGPG::create($file, $storage, UsersImport::class);
        }
    }
    
    public function showFilesImport(){
         return Import::all();
    }
}
```

## Features

- Allows uploading images to storage easily
- Allows you to generate multiple sizes of an image with its corresponding quality settings

## Libraries

- Image Intervention https://image.intervention.io/v2/introduction/installation

##  License

The MIT License (MIT)

Copyright (c) 2023 W360 S.A.S

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
