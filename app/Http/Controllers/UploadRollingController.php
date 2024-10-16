<?php

namespace App\Http\Controllers;

use App\Imports\RollingImport;
use App\Models\Rolling;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UploadRollingController extends Controller
{
    public function index(Request $request){

        // Inisialisasi query untuk mengambil data dari tabel uploadbonus
        $query = Rolling::query();

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
        $twoMonthsAgo = Carbon::now()->today();
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
        $uploadrolling = $query->orderBy('id', 'desc')->paginate(10)->appends($request->query());

        // dd($query->toSql(), $query->getBindings());
       // Kembalikan view dengan data
        return view('pages.uploads.rolling.uploadrolling',compact('uploadrolling'));
    }

    public function store(Request $request){
        // dd($Request->all());
          // $hasil=Excel::import(new BonusImport,$Request->file('excel_file'));
          // dd($hasil);
          // return redirect()->back()->with('success', 'Data berhasil diupload!');;

          $request->validate([
              'excel_file' => 'required|mimes:xlsx,xls,csv',
          ]);

          try {
              // Dapatkan `idupload` baru
             $idUpload = $this->generateIdUpload();

             // Proses import
             // Excel::import(new BonusImport, $request->file('excel_file'));

             // Lakukan proses import file Excel dengan `idupload` baru
             Excel::import(new RollingImport($idUpload), $request->file('excel_file'));

             return redirect()->back()->with('success', 'File Excel berhasil diupload!');

             // Ambil data yang baru saja diupload berdasarkan `idupload`
            //  $uploadedData = Rolling::where('idupload', $idUpload)->get();

            //  if ($uploadedData->isEmpty()) {
            //      // Jika tidak ada data yang diupload
            //      return redirect()->back();
            //  } else {
            //      // Jika data tersedia, kirim ke view
            //      return redirect()->back()->with('success', 'File Excel berhasil diupload!')->with('uploadedDataRolling', $uploadedData);
            //  }

         } catch (\Exception $e) {
             // Jika terjadi error
             return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupload file: ' . $e->getMessage());
         }

      }

    private function generateIdUpload()
    {
        // Ambil tahun dan bulan saat ini
        $year = now()->format('y'); // Mengambil 2 digit tahun
        $month = now()->format('m'); // Mengambil 2 digit bulan
        $vari="RL";

        // Buat format tahun dan bulan
        $prefix =$vari . $year . $month;

        // Ambil record terakhir berdasarkan idupload yang dimulai dengan prefix
        $lastUpload = Rolling::where('idupload', 'like', $prefix . '%')
            ->orderBy('idupload', 'desc')
            ->first();

        // Jika ada record, ambil 4 digit terakhir untuk mendapatkan nomor urut terakhir
        if ($lastUpload) {
            $lastNumber = (int)substr($lastUpload->idupload, -4); // Ambil 4 digit terakhir
        } else {
            // Jika belum ada, mulai dari 0
            $lastNumber = 0;
        }

        // Tambah 1 untuk nomor urut baru
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT); // Format menjadi 4 digit

        // Gabungkan prefix dengan nomor urut baru
        $newIdUpload = $prefix . $newNumber;

        return $newIdUpload;
    }

       //edit
       public function edit($id)
       {
           $uploadrollings = Rolling::find($id);
           return view('pages.uploads.rolling.edit', compact('uploadrollings'));
       }

       //update
       public function update(Request $request, $id)
       {
           $request->validate([
               'member' => 'required',
               'total' => 'nullable|numeric',
           ]);

           $data = Rolling::find($id);
           $data->member = $request->member;
           // $data->totaldepo = $request->total;
           $data->save();


           return redirect()->route('uploadsrolling.index')->with('success', 'updated successfully.');
       }

       public function send($id)
       {
           try {
               $cashback = Rolling::findOrFail($id);

               // Contoh logika pengiriman, sesuaikan dengan kebutuhan

               if ($cashback->status == 3) {
                   return redirect()->back()->with('error', 'Bonus tidak diproses. Tidak dapat dikirim.');
               }

               // Inisialisasi objek BonusImport
               $import = new RollingImport('123');
               $response = $import->sendBonusApi(
                   $cashback->member,      // Member
                   $cashback->total,       // Bonus
                   Auth::user()->name,// Nama user yang aktif
               );

               if ($response['success']) {
                   // Update status jika berhasil
                   $cashback->status = 1; // Contoh: Status berhasil dikirim
                   $cashback->responseapi = json_encode($response); // Simpan respons API
                   $cashback->save();

                   return redirect()->back()->with('success', 'Bonus berhasil dikirim!');
               } else {
                   $cashback->responseapi = json_encode($response); // Simpan respons API
                   $cashback->save();
                   return redirect()->back()->with('error', $response['message']);
               }
           } catch (\Exception $e) {
               return redirect()->back()->with('error', 'Gagal mengirim bonus: ' . $e->getMessage());
           }
       }
}
