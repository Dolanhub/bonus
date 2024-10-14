<?php

namespace App\Exports;

use App\Models\Bonus;
use App\Models\upload;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BonusesExport implements FromCollection, WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Bonus::query();

        if ($this->request->filled('idupload')) {
            $query->where('idupload', $this->request->idupload);
        }

        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereBetween('created_at', [$this->request->start_date, $this->request->end_date]);
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('member', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        return $query->get();
    }
    public function headings(): array
    {
        return [
            'ID',
            'ID Upload',
            'User ID',
            'Member',
            'Total',
            'Status',
            'Created At',
        ];
    }

    public function map($bonus): array
    {
        return[
            $bonus->id,
            $bonus->idupload,
            $bonus->user ? $bonus->user->name : 'Unknown',
            $bonus->member,
            $bonus->total,
            $this->getStatusLabel($bonus->status),
            $bonus->created_at ? $bonus->created_at->format('Y-m-d') : 'No Date',

        ];
    }

    private function getStatusLabel($status)
{
    switch ($status) {
        case 0:
            return 'Pending';
        case 1:
            return 'Success';
        case 2:
            return 'Error';
        case 3:
            return 'UnProses';
        default:
            return 'Unknown';
    }
}
}
