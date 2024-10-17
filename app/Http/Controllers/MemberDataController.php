<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MemberDataController extends Controller
{
    //
    public function index(){
        // return view('pages.memberdata.index', ['data' => null, 'error' => null]);
        return view('pages.memberdata.index', ['member' => null, 'history' => null, 'latesdepo' => null]);
        // return view('pages.memberdata.index');
    }

    public function store(Request $request)
    {
         // Ambil input dari form
         $user = $request->input('member');

         // Base URL dari config
         $baseUrl = config('services.api.url');

         // Panggil API untuk informasi member
        //  $memberResponse = Http::get("{$baseUrl}/player_info", ['user' => $user]);
         $memberResponse=Http::get("{$baseUrl}/player_info/".$user);
         $historyResponse=Http::get("{$baseUrl}/history/dps/".$user);
         $latesdepoResponse=Http::get("{$baseUrl}/latest/dps/".$user);
        // dd($historyResponse);
        //  $memberResponseData =  $memberResponse->json();
         $historyResponseData=$historyResponse->json();
         $latesdepoResponseData=$latesdepoResponse->json();
         return view('pages.memberdata.index', [
            'member' => $memberResponse->json(),
            'history' => $historyResponseData['data'],
            'latesdepos' =>$latesdepoResponseData['data'],
        ]);

        //  // Jika kedua API berhasil
        //  if (!$memberResponse->successful()) {
        //     return view('pages.memberdata.index', [
        //         'data' => null,
        //         'history' => null,
        //         'error' => $memberResponse->json()['message'] ?? 'Error retrieving member info.',
        //     ]);
        // }

        // // Cek apakah API riwayat deposit sukses
        // $historyResponseData = $historyResponse->json();
        // if (!$historyResponse->successful()) {
        //     return view('pages.memberdata.index', [
        //         'data' => $memberResponse->json(),
        //         'history' => null,
        //         'error' => $historyResponseData['message'] ?? 'Error retrieving deposit history.',
        //     ]);
        // }

        // $latesdepoResponseData = $latesdepoResponse->json();
        // if (!$latesdepoResponse->successful()) {
        //     return view('pages.memberdata.index', [
        //         'data' => $latesdepoResponse->json(),
        //         'history' => null,
        //         'error' => $latesdepoResponse['message'] ?? 'Error retrieving deposit history.',
        //     ]);
        // }

        // // Ambil data history
        // if (isset($historyResponseData['data']) && is_array($historyResponseData['data'])) {
        //     $historyData = $historyResponseData['data'];
        // } else {
        //     $historyData = [];
        // }

        // // Tampilkan hasil jika kedua API sukses
        // return view('pages.memberdata.index', [
        //     'data' => $memberResponse->json(),
        //     'history' => $historyData,
        //     'error' => null,
        // ]);
     }

}
