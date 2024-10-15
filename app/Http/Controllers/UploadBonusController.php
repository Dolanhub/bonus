<?php

namespace App\Http\Controllers;

use App\Imports\BonusImport;
use App\Models\Bonus;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UploadBonusController extends Controller
{
    //
    public function index(Request $request){

        $query = Bonus::query();


        // Tambahkan filter untuk hanya menampilkan data Hari ini
        $toDayData = Carbon::now()->today();
        $query->where('created_at', '>=', $toDayData)->where('status',2);

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
        $uploadedData = $query->orderBy('id', 'desc')->paginate(10)->appends($request->query());
        return view('pages.uploads.bonus.uploadbonus', compact('uploadedData'));

    }

    public function store(Request $request){
      // dd($Request->all());
        // $hasil=Excel::import(new BonusImport,$Request->file('excel_file'));
        // dd($hasil);
        // return redirect()->back()->with('success', 'Data berhasil diupload!');;

        $setting = Setting::find(1);
        if (!$setting) {
            return redirect()->back()->with('error', 'Data Setting Belum Ada. Mohon tambahkan data setting terlebih dahulu.');
        }

        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            // Dapatkan `idupload` baru
           $idUpload = $this->generateIdUpload();

           // Proses import
           // Excel::import(new BonusImport, $request->file('excel_file'));

           // Lakukan proses import file Excel dengan `idupload` baru
           Excel::import(new BonusImport($idUpload), $request->file('excel_file'));

           return redirect()->back()->with('success', 'File Excel berhasil diupload!');

        //    // Ambil data yang baru saja diupload berdasarkan `idupload`
        //    $uploadedData = Bonus::where('idupload', $idUpload)->get();

        //    if ($uploadedData->isEmpty()) {
        //        // Jika tidak ada data yang diupload
        //        return redirect()->back();
        //    } else {
        //        // Jika data tersedia, kirim ke view
        //        return redirect()->back()->with('success', 'File Excel berhasil diupload!')->with('uploadedData', $uploadedData);
        //    }

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

        // Buat format tahun dan bulan
        $prefix = $year . $month;

        // Ambil record terakhir berdasarkan idupload yang dimulai dengan prefix
        $lastUpload = Bonus::where('idupload', 'like', $prefix . '%')
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
        $uploadBonuses = Bonus::find($id);
        return view('pages.uploads.bonus.edit', compact('uploadBonuses'));
    }

    //update
    public function update(Request $request, $id)
    {
        $request->validate([
            'member' => 'required',
            'total' => 'nullable|numeric',
        ]);

        $data = Bonus::find($id);
        $data->member = $request->member;
        // $data->totaldepo = $request->total;
        $data->save();


        return redirect()->route('uploadsbonus.index')->with('success', 'updated successfully.');
    }

    public function send($id)
    {
        try {
            $bonus = Bonus::findOrFail($id);

            // Contoh logika pengiriman, sesuaikan dengan kebutuhan

            if ($bonus->status == 3) {
                return redirect()->back()->with('error', 'Bonus tidak diproses. Tidak dapat dikirim.');
            }

            // Inisialisasi objek BonusImport
            $import = new BonusImport('123');
            $response = $import->sendBonusApi(
                $bonus->member,      // Member
                $bonus->bonus,       // Bonus
                Auth::user()->name,// Nama user yang aktif
            );

            /*
            disini bonus tidak diceklagi karena sudah di cek dan sudah di update ketable rekaps
            jadi walau di upload ke api lagi tidak masalah karena sudah dihitung
             */

            if ($response['success']) {
                // Update status jika berhasil
                $bonus->status = 1; // Contoh: Status berhasil dikirim
                $bonus->responseapi = json_encode($response); // Simpan respons API
                $bonus->save();

                return redirect()->back()->with('success', 'Bonus berhasil dikirim!');
            } else {
                $bonus->responseapi = json_encode($response); // Simpan respons API
                $bonus->save();
                return redirect()->back()->with('error', $response['message']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim bonus: ' . $e->getMessage());
        }
    }

}
