<?php

namespace App\DataTables;

use App\User;
use App\FormGroup;
use Yajra\DataTables\Services\DataTable;
use DB;
use Auth;
use App\Form;
use App\FormTable;
use App\FormScheduleForm;
use App\Http\Controllers\Web\FormTabularDataController;
use Illuminate\Support\Facades\Schema;


class FormsSubmissionsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        return datatables($query)->addColumn('action', function ($query){
                $url=url('admin/form/viewTabularData/'.base64_encode($query['form_id']).'?user_id='.base64_encode($query['user_id']).'&schedule_id='.base64_encode($query['schedule_id']));
                   
                   $button = '';

                if($query->status == 1 || $query->status == 2 ){

                  if(strtolower($query->form_type) == strtolower('Tabular')){
                   $button .= '<a href="'.$url.'" class="btn btn-info btn-xs btn-block btn-flat">View Submission</a> ';
                  }

                  if(strtolower($query->form_type) == strtolower('Vertical')){
                   $button .= '<a href="'.route('data/show',['form_id' => base64_encode($query->form_id) , 'schedule_id' => base64_encode($query->schedule_id) , 'user_id' => base64_encode($query->user_id)]).'" class="btn btn-info btn-xs btn-block btn-flat">View Submission</a> ';
                  }

                 }

                if($query->status == 1 && $query->user_submission_request == 0 && $query->record_accept_status == 0 && $query->admin_resubmission_request == 0){
                   $button .='<button style="margin-top:4px;" schedule-id="'.base64_encode($query['schedule_id']).'" form-id="'.base64_encode($query['form_id']).'" user-id="'.base64_encode($query['user_id']).'" class="btn btn-success btn-accept btn-flat btn-block btn-xs">Accept Record</button>';                  
                }

                if($query->user_submission_request == 0 && $query->status == 1  && $query->admin_resubmission_request == 0){
                    $button.='<button schedule-id="'.base64_encode($query['schedule_id']).'" form-id="'.base64_encode($query['form_id']).'" user-id="'.base64_encode($query['user_id']).'" class="btn btn-danger btn-resubmission btn-flat btn-block btn-xs">Re-submission</button>';    
                }

                if(empty($query->status) || is_null($query->status) || $query->status == '0'){
                   $button .= '<button class="btn btn-warning btn-block btn-flat btn-xs">Form not submited yet</button>';
                }

                if($query->user_submission_request == 1 && $query->status == 1){
                    $button .='<button style="margin-top:4px;" schedule-id="'.base64_encode($query['schedule_id']).'" form-id="'.base64_encode($query['form_id']).'" user-id="'.base64_encode($query['user_id']).'" class="btn btn-default btn-accept-request btn-flat btn-block btn-xs">Accept Resubmission Request</button>';
                }

                return $button;

              })
        ->addIndexColumn()
        ->addColumn('status', function($query){
            if ($query['status'] == 1) {
                if($query['record_accept_status'] == 1){
                   return '<span class="label label-success">Record Accepted</span>';
                }
              return '<span class="label label-success">Submitted</span>';
            }elseif($query['status'] == 2){
               return '<span class="label label-warning">Resubmit Form Pending</span>';
            }else{
              return '<span class="label label-danger">Pending</span>';
            }
               })
        ->editColumn('submitted_date', function($query){
               if(empty($query->submitted_date) || is_null($query->submitted_date)){
                        return '-';
               }else{
                        return $query->submitted_date;
               }
         })
        // ->editColumn('user_resubmission_reason',function($query){
        //      if(!empty($query->user_resubmission_reason) && !is_null($query->user_resubmission_reason))
        //            return $query->user_resubmission_reason;
        //            return '-';
        // })
        // ->editColumn('admin_resubmission_reason',function($query){
        //      if(!empty($query->admin_resubmission_reason) && !is_null($query->admin_resubmission_reason))
        //            return $query->admin_resubmission_reason;
        //            return '-';
        // })
        ->rawColumns(['action','status']);
                
    }

    public function query(User $model)
    {
       $form_id = $this->request()->get('id');
       $schedule_id = $this->request()->get('schedule_id');

       $form_id = base64_decode($form_id);
       $schedule_id = base64_decode($schedule_id);

       $forms = FormScheduleForm::select(DB::raw("CONCAT(users.first_name,' ',users.last_name) as user_name"),'users.id as user_id','submit.created_at','submit.schedule_id' , 'submit.submit_status as status' , 'forms.name as form_name' , 'submit.submitted_date' , 'forms.id as form_id' , 'submit.record_accept_status' , 'submit.user_submission_request as user_submission_request' , 'submit.user_request_status' , 'submit.admin_resubmission_request' , 'forms.form_type' , 'submit.user_resubmission_reason' , 'submit.admin_resubmission_reason')
                    ->join('forms' , 'form_scheduled_forms.form_id' , 'forms.id')
                    ->join('form_scheduled_users' , 'form_scheduled_forms.schedule_id' , 'form_scheduled_users.schedule_id')
                    ->join('users', 'form_scheduled_users.user_id' , 'users.id')
                    ->leftJoin('user_form_submitted AS submit', function($join){
                        $join->on('forms.id', '=', 'submit.form_id')
                             ->on('form_scheduled_users.user_id', '=', 'submit.user_id')
                             ->on('form_scheduled_users.schedule_id', '=', 'submit.schedule_id');
                    })
                    ->where('form_scheduled_forms.form_id',$form_id)
                    ->groupBy('submit.user_id','submit.schedule_id','submit.form_id')
                    // ->distinct('submit.user_id')
                    // ->distinct('submit.schedule_id')
                    // ->distinct('submit.form_id')
                    ->where('submit.schedule_id',$schedule_id)
                    ->get();
     
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
        return[
           'DT_Row_Index'=>['title' => 'Sr.No' , 'orderable' => false ,'searchable' => false , 'width' => '5px'] , 
            "form_name" => ['title' => 'Form'],
            "user_name" => ['title' => 'User'],
            "submitted_date",
            "status" => ['title' => 'Submit Status' ]
            // 'user_resubmission_reason',
            // 'admin_resubmission_reason'
            ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'FormGroup_' . date('YmdHis');
    }
}
