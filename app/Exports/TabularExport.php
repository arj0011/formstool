<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TabularExport implements FromView, ShouldAutoSize
{
   private $data;

    public function __construct($data=null)
    {
        $this->data =$data;
    }
    public function view(): View
    {
       $data=$this->data;
       return view('exports.tabularData',compact('data'));
    }
}