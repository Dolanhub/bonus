<?php

namespace App\Imports;

use App\Models\CashBack;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class CashBackImport implements ToCollection,ToModel
{
    private $current = 0;
    protected $idUpload;
    protected $baseUrl;
    /**
    * @param Collection $collection
    */

    public function __construct($idUpload)
    {
        $this->idUpload = $idUpload;
        $this->baseUrl = config('services.api.url'); // Ambil URL API dari config
    }

    public function collection(Collection $collection)
    {
        //
    }

    public function model(array $row){

        $this->current++;
        // Ambil nama user aktif
        $userName = Auth::user()->name;
        if($this->current > 1)
        {
            // Kirim API dengan data bonus dan informasi member
            $apiResponse  = $this->sendBonusApi($row[0], $row[1], $userName);

            $status = $apiResponse['success'] ? 1 : 2;

            // $data = new CashBack();
            // $data->idupload = $this->idUpload;
            // $data->user_id = Auth::user()->id;
            // $data->member = $row[0];
            // $data->total = $row[1];
            // $data->save();
            return new CashBack([
                'idupload'   => $this->idUpload,
                'user_id'    => Auth::id(),
                'member'     => $row[0],
                'total'  => $row[1],
                'status'     => $status,
                'responseapi' => json_encode($apiResponse),
            ]);
        }
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
}
