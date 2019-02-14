<?php

namespace App\DataTables;

use App\User;
use App\UserFormSubmit;
use App\UserSchedule;
use App\FormScheduleForm;
use Yajra\DataTables\Services\DataTable;
use DB;


class UsersReportDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
                // ->editColumn('submit_status', function($query){
                //     if ($query->record_accept_status == 1)
                //         return 'Accepted';
                //     elseif ($query->submit_status == 1)
                //         return 'submited';
                //     elseif ($query->submit_status == 2)
                //         return 'Pending for re-submission';
                //     else
                //     return 'not submited';
                // })
                ->editColumn('role', function($query){
                   if(!empty($query->role) && !is_null($query->role) && is_null($query->role_deleted_at))
                      return $query->role;
                   else
                      return '-';
                })
                ->editColumn('group', function($query){
                   if(!empty($query->group) && !is_null($query->group) && is_null($query->group_deleted_at))
                      return $query->group;
                  else
                      return '-';

                })
                // ->editColumn('action',function($query)
                // {
                //     return '<a href = "#">View</a>';
                // })
                ->editColumn('user_name',function($query)
                {
                    return $query->user_name.' '.$query->last_name;
                })
                ->editColumn('view_details', function($query){
                $url = 'view_details';
                $schedule_id = base64_encode($query->schedule_id);
                $user_id = base64_encode($query->user_id);
                    return  '<a href="'.$url.'?user_id='.$user_id.'&schedule_id='.$schedule_id.'">View Details<a>';
                })
                ->rawColumns(['view_details']);
              //   ->filterColumn('user_name', function($query, $keyword) {
              //       $query->whereRaw("CONCAT(users.first_name,'',users.last_name) like ?", ["%{$keyword}%"]);
              // })
              // ->filterColumn('group', function($query, $keyword) {
              //       $query->whereRaw("user_groups.group_name like ?", ["%{$keyword}%"]);
              // })
              // ->filterColumn('role', function($query, $keyword) {
              //       $query->whereRaw("roles.name like ?", ["%{$keyword}%"]);
              // })
              // ->filterColumn('district', function($query, $keyword) {
              //       $query->whereRaw("districs.distric like ?", ["%{$keyword}%"]);
              // })
              // ->filterColumn('schedule_name', function($query, $keyword) {
              //       $query->whereRaw("form_schedules.schedule_name like ?", ["%{$keyword}%"]);
              // })
              // ->filterColumn('start_date', function($query, $keyword) {
              //   $sdate = date('Y-m-d',strtotime($keyword));
              //     $query->whereRaw("form_schedules.start_date like ?", ["%{$sdate}%"]);
              // })
              // ->filterColumn('end_date', function($query, $keyword) {
              //   $edate = date('Y-m-d',strtotime($keyword));
              //     $query->whereRaw("form_schedules.end_date like ?", ["%{$edate}%"]);
              // });
              // ->filterColumn('created_date', function($query, $keyword) {
              //   $cdate = date('Y-m-d',strtotime($keyword));
              //     $query->whereRaw("form_schedules.created_at like ?", ["%{$cdate}%"]);
              // });
                // ->order(function ($query) {
                //     if (request()->has('order_id')) {
                //         $query->orderBy('order_id', 'desc');
                //     }
                // });
               
    }

    public function query(UserSchedule $model)
    {
         $data = $model->newQuery()
                       ->select('users.city','users.id as user_id', 'form_scheduled_users.schedule_id','users.first_name as user_name','users.last_name' , 'users.role as role_id','users.group_id','form_schedules.schedule_name','form_schedules.start_date','form_schedules.end_date','form_scheduled_users.id as order_id','user_groups.group_name as group','roles.name as role','districs.distric as district','roles.deleted_at as role_deleted_at','user_groups.deleted_at as group_deleted_at')
                     ->join('users' , 'users.id' , '=' , 'form_scheduled_users.user_id')
                     ->Join('roles','users.role','roles.id')
                     ->Join('districs','users.distric_id', '=' , 'districs.id')
                     ->Join('user_groups' , 'user_groups.id' , '=' , 'users.group_id')
                     ->join('form_schedules' , 'form_schedules.id' , '=' , 'form_scheduled_users.schedule_id')
                     ->orderBy('form_scheduled_users.schedule_id', 'desc');
                     if($this->request()->get('role')){
                        $data->where('users.role',$this->request()->get('role'));
                    }
                    if($this->request()->get('group')){
                        $data->where('users.group_id',$this->request()->get('group'));
                    } 

                   $data = $data->get();
                      
                     foreach ($data as $key => $value) 
                     {
                        $scheduled_form[$key]['form_ids'] = FormScheduleForm::where('schedule_id',$value->schedule_id)->select('form_id')->get()->toArray();
                        $scheduled_form[$key]['user_id'] = $value->user_id;
                        $scheduled_form[$key]['schedule_id'] = $value->schedule_id;  
                     }

                     foreach ($scheduled_form as $key1 => $value1) 
                     {
                        
                        foreach ($value1['form_ids'] as $key2 => $value2) 
                        {
                           $form_id_data[$key1] = $value2['form_id'];
                        }
                        $form_submited_or_not[] = DB::table('user_form_submitted')
                                                       ->select('submit_status')
                                                       ->where('user_id',$value1['user_id'])
                                                       ->where('schedule_id',$value1['schedule_id'])
                                                     ->whereIn('form_id',$form_id_data)
                                                       ->get()->toArray();
                                              
                     
                      }
                      foreach ($form_submited_or_not as $key3 => $value3) 
                      {
                          if (!empty($value3)) 
                          {
                            $data[$key3]['submit_status'] = 'Submited';
                          }
                          else
                          {
                            $data[$key3]['submit_status'] = 'Not Submited';
                            
                          }
                      }

                return $this->applyScopes($data);
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
                    // ->addAction(['width' => '80px'])
                    ->parameters($this->getBuilderParameters())
                    ->parameters([
                        'order' => [
                            5, // here is the column number
                            'desc'
                        ]
                    ]);
    }

  
    protected function getColumns()
    {
        return [
            'user_name',
            'group',
            'role',
            'district',
            'schedule_name',
            'start_date',
            'end_date',
            'submit_status'=>['orderable' => false , 'searchable' => false ],
            'view_details'
        ];
    }

    protected function filename()
    {
        return 'UsersReport_' . date('YmdHis');
    }
}
