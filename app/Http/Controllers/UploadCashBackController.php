<?php

namespace App\Http\Controllers;

use App\Imports\CashBackImport;
use App\Models\CashBack;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UploadCashBackController extends Controller
{
    public function index(){
        return view('pages.uploads.uploadcashback');
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
             Excel::import(new CashBackImport($idUpload), $request->file('excel_file'));

             // Ambil data yang baru saja diupload berdasarkan `idupload`
             $uploadedData = CashBack::where('idupload', $idUpload)->get();

             if ($uploadedData->isEmpty()) {
                 // Jika tidak ada data yang diupload
                 return redirect()->back();
             } else {
                 // Jika data tersedia, kirim ke view
                 return redirect()->back()->with('success', 'File Excel berhasil diupload!')->with('uploadedDataBonus', $uploadedData);
             }

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
          $vari="CB";

          // Buat format tahun dan bulan
          $prefix =$vari . $year . $month;

          // Ambil record terakhir berdasarkan idupload yang dimulai dengan prefix
          $lastUpload = CashBack::where('idupload', 'like', $prefix . '%')
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

}
