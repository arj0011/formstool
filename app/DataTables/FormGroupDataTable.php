<?php

namespace App\DataTables;

use App\User;
use App\FormGroup;
use Yajra\DataTables\Services\DataTable;
use DB;
use Auth;
use Request;

class FormGroupDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
     public  $colum='';
     public  $url='';
     public $raw_colum='';
     public $button_title='';
     public $role='';
    
     public function __construct()
     {

      $this->role=Auth::user()->role;
     }
   
    public function dataTable($query)
    {
     
      if($this->type=="submissions")   
      {
        $this->colum="view_submissions"; 
        $this->url=url('admin/form/');
        $this->raw_colum='view_submissions';
        $this->button_title="View Submissions";
        $this->type=base64_encode("submissions");

         return datatables($query)
                ->addIndexColumn()
                ->addColumn('status', function(FormGroup $group){
                    if($group->status == 1)
                         $status = 'Active';
                     else
                         $status = 'Inactive';
                    return $status;
               })
               
           ->addColumn($this->colum, function(FormGroup $group){
                    $view_forms='<a href="'.$this->url.'/'.base64_encode($group->id).'? type='.$this->type.'">'.$this->button_title.'<a>';
                    return $view_forms;
               }) 
          // ->removeColumn('action')
           ->rawColumns([$this->raw_colum]);
              // ->order(function ($query) {
              //       if (request()->has('id')){
              //           $query->orderBy('id', 'desc');
              //  }});
      }else{
        $this->url=url('admin/form');
        $this->raw_colum="view_forms";
        $this->button_title="View Forms";
        $this->colum="view_forms";
        $this->type=base64_encode("forms");

         return datatables($query)
                ->addIndexColumn()
                ->addColumn('status', function(FormGroup $group){
                    if($group->status == 1)
                         $status = 'Active';
                     else
                         $status = 'Inactive';
                    return $status;
               })
                ->addColumn('action',function(FormGroup $group){

                    $button = '<button 
                                  class="btn btn-primary btn-xs modal-btn" btn-action="update" data-id="'.$group->id.'">
                                  <i class="fa fa-edit"></i>
                                 </button> ';

                    $status = $group->status == 1 ? 'ban' : 'check';
                    $title  = $group->status == 1 ? 'Inactive' : 'active'; 

                    $button .= '<a
                                  class="btn btn-default btn-xs btn-status" data-id="'.$group->id.'"  data-toggle="tooltip" title="'.$title.'">
                                  <i class="fa fa-'.$status.'"></i>
                                 </a> ';

                    $button .= '<button 
                                  class="btn btn-danger btn-xs btn-dlt" data-id="'.$group->id.'">
                                  <i class="fa fa-trash"></i>
                                 </button> ';

                 return $button;

                })
            
             ->addColumn($this->colum, function(FormGroup $group){
                    $view_forms='<a href="'.$this->url.'/'.base64_encode($group->id).'? type='.$this->type.'">'.$this->button_title.'<a>';
                    return $view_forms;
               }) 
         
            ->rawColumns([$this->raw_colum,'action']);
              // ->order(function ($query) {
              //       if (request()->has('id')) {
              //           $query->orderBy('id', 'desc');
              //  }});
           }
        
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
       // return $model->newQuery()->select('id', 'created_at', 'updated_at');

       $type=base64_decode($this->request->get('type'));
     
       $user        = Auth::user();
       $schedule_id = $this->request()->get('schedule_id');
       if(!empty($schedule_id) && !is_null($schedule_id)){
            $schedule_id = base64_decode($schedule_id);
       }
       $role=$user->role;
       if($role==1){
        
        if($type == 'submissions'){

          $groups = FormGroup::select('form_groups.id' , 'form_groups.group_name','form_groups.created_at','form_groups.updated_at' , 'form_groups.status')
                                ->join('forms_by_groups' , 'form_groups.id' , 'forms_by_groups.group_id')
                                ->join('form_scheduled_forms' , 'forms_by_groups.form_id' , 'form_scheduled_forms.form_id')
                                ->groupBy('form_groups.id')
                                ->withTrashed()->get('10');
        }else{
          $groups = FormGroup::select('form_groups.id' , 'form_groups.group_name','form_groups.created_at','form_groups.updated_at' , 'form_groups.status')
                                ->get('10');
        }

       }else{
         $groups_id=DB::table('form_scheduled_users as fsu')
                              ->leftjoin('form_scheduled_forms as sf','sf.schedule_id','=','fsu.schedule_id')
                              ->leftjoin('forms_by_groups as fg','fg.form_id','=','sf.form_id')
                              ->join('form_schedules','form_schedules.id','=','fsu.schedule_id')
                              ->select('fg.group_id')
                              ->where('fsu.user_id',$user->id)
                              ->whereDate('form_schedules.start_date', '<=' , date('Y-m-d'))
                              ->where(function($query) use ($schedule_id) {
                                  if(!empty($schedule_id) && !is_null($schedule_id)){
                                       $query->where('sf.schedule_id',$schedule_id);
                                  }
                               })
                              ->pluck('group_id');   
         if(count($groups_id)>0)
         {
           $groups = FormGroup::select('form_groups.id' , 'form_groups.group_name','form_groups.created_at','form_groups.updated_at' , 'form_groups.status')
                                ->whereIn('id',$groups_id)
                               ->withTrashed()->get('10');
         }else $groups=[];
       }
      return $this->applyScopes($groups);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $columns=$this->getColumns();
        if($this->role==1)
        {
            if($this->type=="submissions")   
            {
                 return $this->builder()
                        ->columns($columns)
                        ->minifiedAjax()
                        ->parameters($this->getBuilderParameters())
                        ->parameters([
                          'order' => [
                            3, // here is the column number
                            'desc'
                          ]
                        ]); 
            }else{
                     return $this->builder()
                        ->columns($columns)
                        ->minifiedAjax()
                        ->addAction(['width' => '100px'])
                        ->parameters($this->getBuilderParameters())
                        ->parameters([
                          'order' => [
                            3, // here is the column number
                            'desc'
                          ]
                        ]); 
              }
        }else{
                 return $this->builder()
                        ->columns($columns)
                        ->minifiedAjax()
                        ->parameters($this->getBuilderParameters())
                        ->parameters([
                          'order' => [
                            3, // here is the column number
                            'desc'
                          ]
                        ]);

        }

        
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        
        if($this->type == "submissions")   
           $this->colum = "view_submissions";   
        else
          $this->colum="view_forms";
 
        return [
            'DT_Row_Index' => ['title' => 'Sr.No' , 'orderable' => false , 'searchable' => false ],
            'group_name',
            'status',
            'created_at',
            'updated_at',
            $this->colum
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
