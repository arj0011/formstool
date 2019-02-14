<?php

namespace App\DataTables;

use App\User;
use App\UserGroup;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class UsersDataTable extends DataTable
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
              ->editColumn('role', function(User $user){
                   if(!empty($user->role) && !is_null($user->role) && is_null($user->role_deleted_at))
                      return $user->role;
                   else
                      return '-';
                })
              ->editColumn('group', function(User $user){
                   if(!empty($user->group) && !is_null($user->group) && is_null($user->group_deleted_at))
                      return $user->group;
                  else
                      return '-';

                })
               ->editColumn('district', function(User $query){
                   if(!empty($query->district) && !is_null($query->district))
                      return $query->district;
                  else
                      return '-';

                })
              ->addColumn('status', function(User $user){
                    if($user->status == 1)
                         $status = 'Active';
                     else
                         $status = 'Inactive';
                    return $status;
               })
              ->addColumn('action' , function(User $user) {
              
                 $button = '<a href="'.route('user/show' , 'id='.$user->id).'"
                                 class="btn btn-info btn-xs" >
                                 <i class="fa fa-info-circle"></i>
                            </a> ';

                 if (Auth::user()->can('update' , User::class)) {
                    $button .= '<a 
                                 href="'.route('user/edit' , 'id='.$user->id).'" 
                                  class="btn btn-primary btn-xs">
                                  <i class="fa fa-edit"></i>
                                </a> ';
                    }

                
                    $status = $user->status == 1 ? 'ban' : 'check';
                    $title  = $user->status == 1 ? 'Inactive' : 'active'; 

                    $button .= '<a
                                  class="btn btn-default btn-xs btn-status" data-id="'.$user->id.'"  data-toggle="tooltip" title="'.$title.'">
                                  <i class="fa fa-'.$status.'"></i>
                                 </a> ';
                     if(Auth::user()->can('delete' , User::class)){
                          $button .= '<button 
                                        class="btn btn-danger btn-xs btn-dlt" data-id="'.$user->id.'">
                                        <i class="fa fa-trash"></i>
                                       </button> ';
                      } 

                if(Auth::user()->role == 1){

                    $button .= '&nbsp;<a class="btn" href="'.route('admin-user-login' , 'id='.$user->id).'"
                             >
                             Login
                         </a>';
                }         

                 return $button;

                })
               ->filterColumn('group', function($query, $keyword) {
                $query->whereRaw("groups.group_name like ?", ["%{$keyword}%"]);
               })
               ->filterColumn('role', function($query, $keyword) {
                $query->whereRaw("roles.name like ?", ["%{$keyword}%"]);
               })
               ->filterColumn('district', function($query, $keyword) {
                $query->whereRaw("districs.distric like ?", ["%{$keyword}%"]);
               });
               // ->filterColumn('status', function($query, $keyword) {
               //  $query->where('users.status',$keyword);
               // });

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $users = User::select('users.id' , 'users.first_name' , 'users.last_name' , 'groups.group_name as  group' , 'roles.name as role' , 'districs.distric as district'  , 'users.email' , 'users.mobile' , 'users.status' ,'users.updated_at' , 'roles.deleted_at as role_deleted_at' , 'groups.deleted_at as group_deleted_at')
                      ->leftJoin('roles','roles.id', '=' , 'users.role')
                      ->leftJoin('user_groups as groups','groups.id', '=' , 'users.group_id')
                      ->leftJoin('districs','users.distric_id', '=' , 'districs.id')
                      ->where('users.role' , '!=' , 1);
                       if ($this->request()->get('role')) {
                           $users->where('role', $this->request()->get('role'));
                       }
                       if ($this->request()->get('group')) {
                           $users->where('groups.id', $this->request()->get('group'));
                       }
                       if ($this->request()->get('distric')) {
                           $users->where('users.distric_id', $this->request()->get('distric'));
                       }

        $users->wherenull('users.deleted_at')->get();
            //  ->orderBy('users.id' , 'DESC')->get();

        return $this->applyScopes($users);
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
            ->parameters([
               'order' => [
                   10,
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
        return [
            'DT_Row_Index' => ['title' => 'Sr.No' , 'orderable' => false , 'searchable' => false ],
            'first_name',
            'last_name',
            'group'   => ['searchable' => true],
            'role',
            'district' => ['title' => 'district' , 'orderable' => true , 'searchable' => true ],
            'email'   => ['title' => 'Email Address'],
            'mobile',
            'status'  => ['title' => 'status' , 'orderable' => false , 'searchable' => false ],
            'action'  => ['orderable' => false, 'searchable' => false , 'exportable' => false , 'printable' => false ],
            'updated_at'=>['visible' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'AdminUsers_' . date('YmdHis');
    }

}
