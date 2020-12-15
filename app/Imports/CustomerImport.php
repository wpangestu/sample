<?php

namespace App\Imports;

use App\Models\User;
// use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            $user = User::create([
                'name'  => $row['nama'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'password' => bcrypt($row['password']),
                'address' => $row['alamat'],
                "userid"    => Str::random(6),
                "is_active" => $row['aktif']??0
            ]);

            $user->assignRole('user');
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
