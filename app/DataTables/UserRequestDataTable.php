<?php

namespace App\DataTables;

use App\UserRequest;
use Yajra\DataTables\Services\DataTable;

class UserRequestDataTable extends DataTable
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
            ->addColumn('action', 'userrequest.action');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(UserRequest $model)
    { 

        $users_request=UserRequest::select('user_requests.id','forms.name as template_name',
                'users.first_name as user_name','user_requests.message','user_requests.status','user_requests.created_at','user_requests.updated_at')
                      ->leftJoin('users','users.id', '=' , 'user_requests.user_id')
                      ->leftJoin('forms','forms.id', '=' , 'user_requests.form_id')
                     ->wherenull('users.deleted_at')
                      ->orderBy('users.id' , 'DESC')->get('10');
    return $this->applyScopes($users_request);
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
                    ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
          return[
                'id',
                'message',
                'template_name',
                "user_name",
                'status',
                'created_at',
                'updated_at'
            ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'UserRequest_' . date('YmdHis');
    }
}
