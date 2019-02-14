<?php

namespace App\Http\Controllers\Web;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\UserRequestDataTable;

class UserRequestController extends Controller
{
    //
public function index(UserRequestDataTable $dataTable){
        return $dataTable->render('users_request.index');
    }
}
