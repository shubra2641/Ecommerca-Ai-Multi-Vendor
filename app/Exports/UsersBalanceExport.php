<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersBalanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Role',
            'Balance',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            ucfirst($user->role),
            number_format($user->balance, 2),
        ];
    }
}
