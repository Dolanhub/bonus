<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection ,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

     protected $request;
     // Terima request pencarian melalui konstruktor
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $request = $this->request->input('search');

        $users = DB::table('users')
        ->when($request,function ($query, $search) {
           return $query->where(function($query) use ($search) {
               $query->where('name', 'like', '%' . $search . '%')
                     ->orWhere('email', 'like', '%' . $search . '%')
                     ->orWhere('role', 'like', '%' . $search . '%');
           });
       })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return $users;

    }

    public function headings(): array
    {
        return[
            'id',
            'Name',
            'Email',
            'Role',
        ];
    }
    public function map($user): array
    {
        return[
            $user->id,
            $user->name,
            $user->email,
            $user->role,
        ];
    }
}
