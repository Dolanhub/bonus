<?php

namespace App\Imports;

use App\Models\Rolling;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class RollingImport implements ToCollection,ToModel
{
    private $current = 0;
    protected $idUpload;
    /**
    * @param Collection $collection
    */

    public function __construct($idUpload)
    {
        $this->idUpload = $idUpload;
    }
    public function collection(Collection $collection)
    {
        //
    }
    public function model(array $row){

        $this->current++;
        if($this->current > 1)
        {
            $data = new Rolling();
            $data->idupload = $this->idUpload;
            $data->user_id = Auth::user()->id;
            $data->member = $row[0];
            $data->total = $row[1];
            $data->save();
        }
    }
}
