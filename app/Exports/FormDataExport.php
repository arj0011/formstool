<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FormDataExport implements FromView , ShouldAutoSize 
{
    public $data = null;

    public function __construct($data = null){
           $this->data = $data;         
    }

    public function view(): View
    {   
        $data =  $this->data;
        return view('exports.verticalData', compact('data'));
    }
}





