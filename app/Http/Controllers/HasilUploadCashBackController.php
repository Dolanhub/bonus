<?php

namespace App\Http\Controllers;

use App\Exports\CashBackExport;
use App\Models\CashBack;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HasilUploadCashBackController extends Controller
{
    public function index(Request $request)
    {

        // Inisialisasi query untuk mengambil data dari tabel uploadbonus
        $query = CashBack::query();

        // Filter berdasarkan idupload jika ada
        if ($request->filled('idupload')) {
            $query->where('idupload', $request->idupload);
        }

        // Filter berdasarkan rentang tanggal created_at jika ada
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('created_at', '>=', $request->start_date)
                  ->whereDate('created_at', '<=', $request->end_date);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Tambahkan filter untuk hanya menampilkan data dalam 2 bulan terakhir
        $twoMonthsAgo = Carbon::now()->subMonths(2);
        $query->where('created_at', '>=', $twoMonthsAgo);

        // Pencarian berdasarkan member dan users.name jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('member', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Mengurutkan hasil berdasarkan id secara descending
        $uploadcashbacks = $query->orderBy('id', 'desc')->paginate(10)->appends($request->query());

        // dd($query->toSql(), $query->getBindings());
       // Kembalikan view dengan data
       return view('pages.hasil.cashback.index', compact('uploadcashbacks'));
    }

    //edit
    public function edit($id)
    {
        $uploadcashbacks = CashBack::find($id);
        return view('pages.hasil.cashback.edit', compact('uploadcashbacks'));
    }

    //update
    public function update(Request $request, $id)
    {
        $request->validate([
            'member' => 'required',
            'total' => 'required',

        ]);

        $uploadcashbacks = CashBack::find($id);
        $uploadcashbacks->member = $request->member;
        $uploadcashbacks->total = $request->total;
        $uploadcashbacks->save();

        return redirect()->route('hasilcashback.index')->with('success', 'updated successfully.');
    }

    //destroy
    public function destroy($id)
    {
        $user = CashBack::find($id);
        $user->delete();

        return redirect()->route('hasilcashback.index')->with('success', 'deleted successfully.');
    }



    public function export(Request $request)
    {

        return Excel::download(new CashBackExport($request), 'cashback.csv', \Maatwebsite\Excel\Excel::CSV);

    }
}
