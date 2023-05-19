<?php

namespace W360\ImportGpgExcel\Imports;

use Illuminate\Support\Facades\Hash;
use W360\ImportGpgExcel\Models\User;

class UsersImport extends GpgImport
{
   /**
    * @return mixed|null
    */
    public function row($row)
    {
        return new User([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => Hash::make('password'),
        ]);
    }

}