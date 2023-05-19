<?php

namespace W360\ImportGpgExcel\Imports;

use Illuminate\Support\Facades\Hash;
use W360\ImportGpgExcel\Models\User;

class UsersImport extends GpgImport
{

    /**
     * @param array $row
     * @return bool
     * @throws \Exception
     */
    public function row(array $row): bool
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