<?php

namespace App\DataTables;

use App\User;
use App\FormSchedule;
use App\UserSchedule;
use Yajra\DataTables\Services\DataTable;
use Auth;
use Illuminate\Http\Request;
use DB;
use App\FormTable;
use App\Form;
use Illuminate\Support\Facades\Schema;
class ScheduleformsDataTable extends DataTable
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
        if($this->user->role == 1){
            return datatables($query)
            ->addIndexColumn()
            ->editColumn('action', function(FormSchedule $formSchedule) {
                $url = 'submissions';
                $schedule_id = base64_encode($formSchedule->id);
                $form_id = base64_encode($formSchedule->form_id);
                $urlTitle = 'View Submission';
                    // return  '<a href="'.$url.'?id='.$form_id.'&schedule_id='.$schedule_id.'"><span class="badge badge-light">'.$formSchedule->submit_count.'</span>&nbsp;&nbsp;'.$urlTitle.'<a>';
                    return  '<a href="'.$url.'?id='.$form_id.'&schedule_id='.$schedule_id.'">'.$urlTitle.'<a>'; 
            });
        }else{
            return datatables($query)
                ->addIndexColumn()
                ->editColumn('status',function(UserSchedule $formSchedule){
                    if($formSchedule->status == 1)
                        return '<span style="color:green" class="l">Active</span>';
                    else
                        return '<span style="color:red" class="">Inactive</span>';
                })
                ->editColumn('record_accept_status',function(UserSchedule $formSchedule){
                    if($formSchedule->record_accept_status == 1 && $formSchedule->submit_status == 1 && $formSchedule->user_submission_request == 0){
                        return '<p style="text-transform: capitalize;"  class="text-success">Accepted Record<p>' ;
                    }else if($formSchedule->submit_status == 1 && $formSchedule->record_accept_status == 0){
                        return '<p style="text-transform: capitalize;"  class="text-danger">Pending<p>' ;
                    }else{
                        return '-';
                    }
                })
                ->editColumn('action', function(UserSchedule $userSchedule) {
                
                    $action = '';
                    if(is_null($userSchedule->submit_status) || $userSchedule->submit_status == '' || $userSchedule->submit_status == 0){
                        $action .= '<a href="'.route("form/submit").'?form_id='.base64_encode($userSchedule->form_id).'&schedule_id='.base64_encode($userSchedule->schedule_id).'"  data-toggle="tooltip" title="Submit Form!" class="submit-template btn btn-flat btn-xs btn-block btn-success">Submit</a> ';
                    }
                    
                    if($userSchedule->submit_status == 1 || $userSchedule->submit_status == 2){
                        if($userSchedule->form_type == 'Vertical'){
                            $action .= '<a href="'.url("show-data?form_id=").base64_encode($userSchedule->form_id).'&schedule_id='.base64_encode($userSchedule->schedule_id).'" data-toggle="tooltip" title="View record!" class="submit-template btn btn-flat  btn-block  btn-xs btn-info">&nbsp;&nbsp;&nbsp;view&nbsp;&nbsp;&nbsp;</a> ';
                        }else{
                            $action .= '<a href="'.url("admin/form/viewTabularData/").'/'.base64_encode($userSchedule->form_id).'?form_id='.base64_encode($userSchedule->form_id).'&schedule_id='.base64_encode($userSchedule->schedule_id).'"  data-toggle="tooltip" title="View record!" class="submit-template btn  btn-block  btn-flat btn-xs btn-info">&nbsp;&nbsp;&nbsp;view&nbsp;&nbsp;&nbsp;</a> ';
                        }
                    }
                        
                    if($userSchedule->submit_status == 1 && $userSchedule->user_submission_request == 0){
                          $action .= '<button form-id="'.base64_encode($userSchedule->form_id).'" schedule-id="'.base64_encode($userSchedule->schedule_id).'" data-toggle="tooltip" title="Request for Resubmission!" class="submit-template btn  btn-block  btn-flat btn-xs btn-danger resubmission">Request for Re-Submission</button> ';
                    }
                   
                    if($userSchedule->submit_status == 2 && $userSchedule->user_submission_request == 2 || $userSchedule->admin_resubmission_request == 1){
                          $action .='<a href="'.route("form/submit").'?form_id='.base64_encode($userSchedule->form_id).'&schedule_id='.base64_encode($userSchedule->schedule_id).'"  data-toggle="tooltip" title="Submit Form!" class="submit-template btn  btn-block  btn-flat btn-xs btn-success">Re-submit</a> ';
                    }

                    if($userSchedule->user_submission_request == 1 ){
                        $action .='<button class="submit-template btn btn-flat btn-xs  btn-block  btn-warning">Re-submittion request is pending</button> ';
                    }
                    return $action;
               
                
                })

                ->rawColumns(['record_accept_status','status','action']);    
        }    
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(UserSchedule $model,Request $request)
    {
        $user_id=Auth::id();
        $schedule_id=base64_decode($request->get('schedule_id'));
        $role=Auth::user()->role;
        if($role == 1){
            
            /* Admin schedule's forms list with submission count */

            $forms  = FormSchedule::select('form_schedules.id','form_schedules.schedule_name','forms.id as form_id','forms.name as form','forms.form_type','form_groups.group_name as group')  
                ->join('form_scheduled_forms' , 'form_scheduled_forms.schedule_id' , '=' , 'form_schedules.id')
                ->join('forms' , 'forms.id' , '=' , 'form_scheduled_forms.form_id')
                ->join('forms_by_groups' , 'forms_by_groups.form_id' , '=' , 'forms.id')
                ->join('form_groups' , 'form_groups.id' , '=' , 'forms_by_groups.group_id')
                ->where('form_schedules.id',$schedule_id)
                ->get();
        }else{
            /* User schedule's forms list */
            $forms = UserSchedule::select('form_scheduled_users.schedule_id as schedule_id','forms.id as form_id' , 'forms.name as form' , 'forms.status'  , 'forms.form_type' , 'form_scheduled_forms.user_readed' , 'form_groups.group_name as group' , 'submit.submit_status' , 'submit.user_submission_request' , 'submit.user_request_status' , 'submit.record_accept_status' , 'submit.admin_resubmission_request' , 'submit.user_resubmission_reason' , 'submit.admin_resubmission_reason' , 'form_schedules.schedule_name' , 'form_schedules.start_date' , 'form_schedules.end_date')
                                  ->join('form_scheduled_forms' , 'form_scheduled_users.schedule_id' , '=' , 'form_scheduled_forms.schedule_id')
                                  ->join('forms' , 'form_scheduled_forms.form_id' , '=' , 'forms.id')
                                  ->join('forms_by_groups' , 'forms.id' , '=' , 'forms_by_groups.form_id')
                                  ->join('form_groups' , 'forms_by_groups.group_id' , '=' , 'form_groups.id')
                                  ->join('form_schedules' , 'form_scheduled_users.schedule_id' , '=' , 'form_schedules.id')
                                  ->leftJoin('user_form_submitted AS submit', function($join){
                                        $join->on('forms.id', '=', 'submit.form_id')
                                             ->on('form_scheduled_users.user_id', '=', 'submit.user_id')
                                             ->on('form_scheduled_users.schedule_id', '=', 'submit.schedule_id');
                                    })
                                  ->where('form_scheduled_users.user_id' , $user_id)
                                  ->whereDate('form_schedules.start_date', '<=' , date('Y-m-d'))
                                  
                                  ->where(function($query) use ($schedule_id){
                                      if (!empty($schedule_id) && !is_null($schedule_id)) {
                                        $query->where('form_scheduled_users.schedule_id' , $schedule_id);
                                      }
                                    })
                                  ->whereNull('form_schedules.deleted_at')
                                 // ->groupBy('submit.user_id','submit.schedule_id','submit.form_id')
                                  ->distinct('submit.user_id','submit.schedule_id','submit.form_id')
                                  ->get();

            
        }
        foreach($forms as $key => $value){
                $form_id=$value->form_id;
                $total=0;
                $is_submitted=0;
                if($value->form_type == "Tabular"){
                    $tables=FormTable::where('form_id',$form_id)->get();
                    if($tables){
                        foreach($tables as $key1=>$table){
                            $table_name=str_replace(' ', '_', strtolower($value->form))."_".$form_id."_".$table->id; 
                            if(Schema::hasTable($table_name)){
                                $check_submissions=DB::table($table_name)
                                                ->select('user_id')
                                                ->where('schedule_id',$schedule_id)
                                                ->groupBy('user_id')
                                                ->pluck('user_id');                                                
                                if($check_submissions) 
                                    $total = $total + count($check_submissions);
                                    $record_submit = DB::table($table_name)
                                        ->where('user_id',$user_id)
                                        ->count();
                                    if($record_submit>0) 
                                        $is_submitted=1;
                            }
                        }
                    }     
                }else{
                    $table=str_replace(' ', '_', strtolower($value->form))."_".$form_id;
                    if(Schema::hasTable($table)){
                        $total=DB::table($table)->where('schedule_id',$schedule_id)->count();
                        $forms[$key]['is_table']     = true;
                        
                    }else{
                        $forms[$key]['is_table']     = false;
                        
                        $total=0;
                    }
                }
                $forms[$key]['submit_count']=$total;
                $forms[$key]['is_submitted']=$is_submitted;
                

            }           
        return $this->applyScopes($forms);
    
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
                    ->addAction(['width' => '100px'])
                    ->parameters($this->getBuilderParameters())
                    ->parameters([
                       'order' => [
                           1, // here is the column number
                           'asc'
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
        if($role==1){
            return [
                'DT_Row_Index' => ['title' => 'Sr.No' , 'orderable' => false , 'searchable' => false ],
                'form',
                'group',
                'form_type',
            ];
        }else{
            return [
                'DT_Row_Index' => ['title' => 'Sr.No' , 'orderable' => false , 'searchable' => false ],
                'form',
                'form_type',
                'group'=>['title'=>'Form Group'],
                'schedule_name',
                'start_date',
                'end_date',
                'status',
                'record_accept_status',
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
