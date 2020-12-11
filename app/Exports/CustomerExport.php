<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection,WithHeadings,WithMapping
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::Role('user')->get();
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->email,
            $customer->address,
            $customer->phone,
            $customer->userid,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Email',
            'Alamat',
            'Phone',
            'Customer ID'
        ];
    }
}
