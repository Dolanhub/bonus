<?php

namespace App\Imports;

use App\Models\Bonus;
use App\Models\Rekap;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Row;

class BonusImport implements ToCollection,ToModel
{
    private $current = 0;
    protected $idUpload;
    protected $baseUrl;
    protected $setting;
    protected $persen;
    /**
    * @param Collection $collection
    */

    public function __construct($idUpload)
    {
        $this->idUpload = $idUpload;
        $this->baseUrl = config('services.api.url'); // Ambil URL API dari config
        $this->setting = Setting::first(); // Mengambil satu record dari tabel setting
        $this->persen = $this->setting->persen / 100; // Ambil persen dari setting dan bagi 100

    }

    public function collection(Collection $collection)
    {

    }

    public function model(array $row){
        $this->current++;

    if ($this->current === 1) {
        return null; // Lewati header
    }

    $member = $row[0]; // Kolom Member
    $total = $row[1];  // Kolom Total dari Excel
    // Ambil nilai persen dan maxbonus dari table setting

    $bonusBaru = $total * $this->persen; // Bonus dihitung dari persen yang diambil dari setting
    $maxBonus = $this->setting->maxbonus; // Ambil nilai maxbonus dari setting
    $tanggal = now()->format('Y-m-d');


    // Cek apakah ada data rekap untuk member dan tanggal ini
    $rekap = Rekap::where('tanggal', $tanggal)
                  ->where('member', $member)
                  ->first();

    if ($rekap) {
        $totalBonusSaatIni = $rekap->bonus; // Bonus saat ini di rekap
        $sisaBonus = $maxBonus - $totalBonusSaatIni; // Hitung sisa bonus

        if ($sisaBonus > 0) {
            // Ada ruang untuk menambah bonus
            $bonusTerpakai = min($bonusBaru, $sisaBonus); // Batasi bonus jika perlu
            $rekap->bonus += $bonusTerpakai; // Tambah bonus ke rekap
            $status = 0; // Status 0 jika masih ada sisa bonus
        } else {
            // Tidak ada ruang, bonus langsung dibatasi
            $bonusTerpakai = 0; // Tidak bisa menambah bonus
            $status = 3; // Status 3 jika tidak ada sisa bonus
        }

        $rekap->save();
    } else {
        // Jika belum ada rekap, buat entri baru
        $bonusTerpakai = min($bonusBaru, $maxBonus); // Batasi bonus jika perlu
        $status = $bonusTerpakai < $maxBonus ? 0 : 3; // Set status sesuai kondisi

        Rekap::create([
            'user_id' => Auth::id(),
            'member'  => $member,
            'bonus'   => $bonusTerpakai,
            'tanggal' => $tanggal,
        ]);
    }


        // Ambil nama user aktif
        $userName = Auth::user()->name;

        // Kirim API dengan data bonus dan informasi member
        $apiResponse  = $this->sendBonusApi($member, $bonusTerpakai, $userName);

        // Tentukan status berdasarkan hasil pengiriman API
        $status = $apiResponse['success'] ? 1 : 2;


        // Simpan data ke table bonuses dengan bonus yang sudah disesuaikan
        // $bonus = new Bonus();
        // $bonus->idupload = $this->idUpload;
        // $bonus->user_id = Auth::id();
        // $bonus->member = $member;
        // $bonus->totaldepo = $total;
        // $bonus->bonus = $bonusTerpakai; // Bonus yang disesuaikan
        // $bonus->status = $status; // Status berdasarkan kondisi sisa bonus
        // $bonus->save();
        // Simpan data ke tabel bonuses

        return new Bonus([
            'idupload'   => $this->idUpload,
            'user_id'    => Auth::id(),
            'member'     => $member,
            'totaldepo'  => $total,
            'bonus'      => $bonusTerpakai,
            'status'     => $status,
            'responseapi' => json_encode($apiResponse),
        ]);
    }

    public function sendBonusApi($member, $bonus, $userName)
    {

        // Bangun URL API dengan parameter dinamis
        $url = sprintf(
            "%s/MANUAL/depo/%s/Bonus/%d/%s",
            $this->baseUrl,    // Base URL dari konfigurasi
            $member,           // Member dari file Excel
            $bonus,            // Bonus terpakai hasil perhitungan
            $userName          // Nama user aktif
        );
            //  dd($url);
        try {

            // Tambahkan jeda 500ms sebelum request
            usleep(250000); // 500.000 mikrodetik = 0,5 detik

            // Kirim GET request ke URL API
            $response = Http::get($url);

            if ($response->successful()) {
                return $response->json(); // Kembalikan respons dari API
            }

            // Jika API mengembalikan error, tampilkan pesan
            logger()->error('API Error: ' . $response->json('message'));
        } catch (\Exception $e) {
            logger()->error('API Request Failed: ' . $e->getMessage());
        }

        // Jika gagal, kembalikan response dengan false
        return [
            'success' => false,
            'message' => 'Failed to send API request'
        ];
    }

    // private function generateIdUpload()
    // {
    //     // Ambil tahun dan bulan saat ini
    //     $year = now()->format('y'); // Mengambil 2 digit tahun
    //     $month = now()->format('m'); // Mengambil 2 digit bulan

    //     // Buat format tahun dan bulan
    //     $prefix = $year . $month;

    //     // Ambil record terakhir berdasarkan idupload yang dimulai dengan prefix
    //     $lastUpload = upload::where('idupload', 'like', $prefix . '%')
    //         ->orderBy('idupload', 'desc')
    //         ->first();

    //     // Jika ada record, ambil 4 digit terakhir untuk mendapatkan nomor urut terakhir
    //     if ($lastUpload) {
    //         $lastNumber = (int)substr($lastUpload->idupload, -4); // Ambil 4 digit terakhir
    //     } else {
    //         // Jika belum ada, mulai dari 0
    //         $lastNumber = 0;
    //     }

    //     // Tambah 1 untuk nomor urut baru
    //     $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT); // Format menjadi 4 digit

    //     // Gabungkan prefix dengan nomor urut baru
    //     $newIdUpload = $prefix . $newNumber;

    //     return $newIdUpload;
    // }
}
