<?php

namespace App\DataTables;

use App\User;
use App\FormSchedule;
use Yajra\DataTables\Services\DataTable;
use Auth;
use Illuminate\Http\Request;
use DB;
use App\FormTable;
use App\Form;
use Illuminate\Support\Facades\Schema;
class ScheduleDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public $user;
   
    public function __construct()
    {
        $this->user=Auth::user();
        
    }
     public function dataTable($query)
    {
        return datatables($query)
              ->addIndexColumn()
              ->editColumn('schedule_for', function(FormSchedule $formSchedule){
                   return  ucwords(str_replace('_' , ' ' ,$formSchedule->schedule_for));
                })
               ->editColumn('start_date', function(FormSchedule $formSchedule){
                   return  date('d M Y' , strtotime($formSchedule->start_date));
                })
               ->editColumn('end_date', function(FormSchedule $formSchedule){
                   return  date('d M Y' , strtotime($formSchedule->end_date));
                })
                // ->editColumn('schedule_date', function(FormSchedule $formSchedule){
                //    return  date('d M Y' , strtotime($formSchedule->schedule_date)).' at '.date('h:i A' , strtotime($formSchedule->schedule_date));
                // })

                ->editColumn('submissions', function(FormSchedule $formSchedule){
                $url = 'schedule-forms';
                $schedule_id = base64_encode($formSchedule->id);
                $urlTitle = 'View Submission';
                    return  '<a href="'.$url.'?'.'schedule_id='.$schedule_id.'">'.$urlTitle.'<a>';
                })

              ->addColumn('action',function(FormSchedule $formSchedule){

                    $button='';
                    if($this->user->role==1)
                    {
                        $button ='<a 
                                      class="btn btn-info btn-xs modal-btn" btn-type="info" title="info" href="'.route('schedule/show' , 'id='.$formSchedule->id).'">
                                      <i class="fa fa-info-circle"></i>
                                     </a> ';

                        $button .= '<button
                                      class="btn btn-primary btn-xs modal-Btn" btn-type="edit"  title="edit"  data-id="'.$formSchedule->id.'">
                                      <i class="fa fa-edit"></i>
                                     </button> ';

                        $button .= '<button 
                                      class="btn btn-danger btn-xs btn-dlt"  title="delete"  data-id="'.$formSchedule->id.'">
                                      <i class="fa fa-trash"></i>
                                     </button> ';

                     return $button;
                    }else{
                        $form_id=$formSchedule->form_id;
                        $schedule_id=$formSchedule->id;
                        $is_submitted=0;
                        $form=Form::find($form_id);
                        $tables=FormTable::where('form_id',$form_id)
                              ->take(1)
                              ->get();
                              if($tables)
                                {
                                 foreach($tables as $key1=>$table){
                                  $table_name=str_replace(' ', '_', strtolower($form->name))."_".$form_id."_".$table->id; 
                                if(Schema::hasTable($table_name)){
                    
                                        $record_submit=DB::table($table_name)
                                                        ->where('user_id',$this->user->id)
                                                        ->where('status','!=',4)
                                                        ->where('schedule_id',$schedule_id)
                                                        ->count();
                                         if($record_submit>0) $is_submitted=1;
                                                 
                                        
                                         }
                                    }
                               }
                    
                        // $view_url=url('form-groups/'.base64_encode($formSchedule->group_id).'?schedule_id='.base64_encode($schedule_id));

                        // $action='<a  href="'.$view_url.'" data-toggle="tooltip" title="View Submitted Data!" class="view-template btn btn-info btn-sm">View</a>';
                        
                        $view_url = 'schedule-forms?schedule_id='.base64_encode($schedule_id);       
                        $action='<a  href="'.$view_url.'" data-toggle="tooltip" title="View Submitted Data!" class="view-template btn btn-info btn-sm">View</a>';
                        return $action;

                    }
                   

                })
                ->rawColumns(['action', 'submissions']);
              
                
                
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model,Request $request)
    {
        $schedules = FormSchedule::select('id','schedule_for' , 'start_date' , 'created_at as schedule_date' , 'schedule_name' , 'end_date')->get('10');

        $form_id=base64_decode($request->form_id);
        $user_id=Auth::id();
        if($this->user->role==1){
            $schedules=FormSchedule::select('id','schedule_for' , 'start_date' , 'created_at as schedule_date' , 'schedule_name' , 'end_date')->get('10');
        }else{

            $schedules=FormSchedule::select('forms_by_groups')
                                    ->join('form_scheduled_users','form_schedules.id','form_scheduled_users.schedule_id')
                                    ->join('form_scheduled_forms','form_scheduled_forms.schedule_id','form_scheduled_users.schedule_id')
                                    ->join('forms_by_groups','forms_by_groups.form_id','form_scheduled_forms.form_id')
                                    ->where('form_scheduled_users.user_id',$user_id)
                                    ->whereDate('form_schedules.start_date', '<=' , date('Y-m-d'))
                                    ->select('form_schedules.id','form_schedules.schedule_for' , 'start_date' , 'form_schedules.created_at as schedule_date' , 'form_schedules.schedule_name' , 'end_date')
                                    ->groupBy('form_schedules.id')
                                    ->get();
    
        }

        return $this->applyScopes($schedules);
    
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->addAction(['width' => '80px'])
                    ->parameters($this->getBuilderParameters())
                    ->parameters([
                       'order' => [
                           2, // here is the column number
                           'desc'
                       ]
                   ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
       
        $role=Auth::user()->role;
        if($role==1)
            {
                return [
                'DT_Row_Index' => ['title' => 'Sr.No' , 'orderable' => false , 'searchable' => false ],
                'schedule_name',
                'schedule_date'  => ['title' => 'Created Date'],
                'schedule_for',
                'start_date',
                'end_date'  => ['title' => 'Expiry Date'],
                'submissions'=>'submissions',
                     ];
             }else{
                    return [
                        'DT_Row_Index' => ['title' => 'Sr.No' , 'orderable' => false , 'searchable' => false ],
                        'schedule_name',
                        'schedule_date'  => ['title' => 'Created Date'],
                        'start_date',
                        'end_date'  => ['title' => 'Expiry Date'],
                     ];
            }
       
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Schedule_' . date('YmdHis');
    }
}
