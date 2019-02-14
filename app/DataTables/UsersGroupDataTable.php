<?php

namespace App\DataTables;

use App\User;
use App\UserGroup;
use Yajra\DataTables\Services\DataTable;

class UsersGroupDataTable extends DataTable
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
                ->addIndexColumn()
                ->editColumn('created_at', function(UserGroup $group){
                   return date('d M Y',strtotime($group->created_at)) .' at '. date('H :i A',strtotime($group->created_at));
                })
                ->editColumn('updated_at', function(UserGroup $group){
                   return date('d M Y',strtotime($group->updated_at)) .' at '. date('H :i A',strtotime($group->updated_at));
                })
                ->addColumn('status', function(UserGroup $group){
                    if($group->status == 1)
                         $status = 'Active';
                     else
                         $status = 'Inactive';
                    return $status;
               })
                ->addColumn('action',function(UserGroup $group){

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
              ->order(function ($query){
                    if (request()->has('id')) {
                        $query->orderBy('id', 'DESC');
               }});

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {

         // $groups=UserGroup::select('user_groups.id' , 'user_groups.group_name','roles.name as role','user_groups.created_at','user_groups.updated_at' , 'user_groups.status')
         //                      ->join('roles','roles.id' , '=' , 'user_groups.role_id')
         //                      ->get('10');

         // return $this->applyScopes($groups);

         $groups = UserGroup::select('user_groups.id' , 'user_groups.group_name', 'user_groups.created_at','user_groups.updated_at' , 'user_groups.status')
                              ->get();

         return $this->applyScopes($groups);

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
                    ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
                    'DT_Row_Index' => ['title' => 'Sr.No' , 'orderable' => false , 'searchable' => false ],
                    'group_name',
                    'status',
                    'created_at',
                    'updated_at',

                    ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'UsersGroup_' . date('YmdHis');
    }
}
