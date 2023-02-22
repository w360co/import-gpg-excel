<?php

namespace W360\ImportGpgExcel\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use W360\ImportGpgExcel\Models\User;

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