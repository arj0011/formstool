<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

class CroneJobController extends Controller
{
    
    public function scheduleFormMail(){

    	$data = DB::table('form_scheduled_users')
    	            ->select('form_schedules.id')
    	            ->join('form_scheduled_forms' , 'form_scheduled_users.schedule_id' , '=' , 'form_scheduled_forms.form_id')
    	            ->join('forms' , 'form_scheduled_forms.form_id' , '=' , 'form.id')
    	            ->join('users' , 'form_scheduled_users.user_id' , '=' , 'users.id')
    	            ->leftJoin('form_schedules' , 'form_scheduled_users.schedule_id' , '=' , 'form_schedules.id')
    	            ->get();

    	            return $data;
    }
}
