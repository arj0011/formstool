<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

// use Illuminate\Contracts\View\View;
// use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

// class UsersExport implements FromView , ShouldAutoSize 
class UsersReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    
    public $users = null;

	public function __construct($data = null){
        foreach ($data as $key => $value) {
            if ($value->record_accept_status == 1)
                $data[$key]->submit_status = 'Accepted';
            elseif ($value->submit_status == 1)
                $data[$key]->submit_status = 'submited';
            elseif ($value->submit_status == 2)
                $data[$key]->submit_status = 'Pending for re-submission';
            else
            $data[$key]->submit_status = 'not submited';   
            unset($data[$key]->record_accept_status); 
        }
        
        $this->users = $data;       
	}

	/**
    *	View Approach
    */
   	/*public function view(): View
   	{   
       $users =  $this->users;
       return view('exports.users', compact('users'));
   	}*/

	/**
	* 	Collection Approach
	*/
	public function collection()
    {
        // return User::all();
        return $this->users;
    }

   	public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Group',
            'Role',
            'District',
            'Schedule Name',
            'Start Date',
            'End Date',
            'Submit Status',
            'Created Date'


        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                // $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $styleArray = [
							    'font' => [
							        'bold' => true,
							    ],
							    'alignment' => [
							        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
							    ],
							    'borders' => [
							        'top' => [
							            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
							        ],
							    ],
							    'fill' => [
							        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
							        'rotation' => 90,
							        'startColor' => [
							            'argb' => 'FFA0A0A0',
							        ],
							        'endColor' => [
							            'argb' => 'FFFFFFFF',
							        ],
							    ],
							];
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
            },
        ];
    }


}
