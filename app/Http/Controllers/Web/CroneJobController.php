<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\NotifyMail;
use Mail;
use DB;

class CroneJobController extends Controller
{
    
    public function scheduleFormMail(){

           if(strtotime(date('h:ia')) == strtotime('10:00am')){
        // if(true){

	        $data  = DB::table('form_schedules')
		                ->select('form_schedules.id as schedule_id' , 'users.first_name' , 'users.last_name' ,'users.email as user_email' , 'form_schedules.schedule_name' ,  'form_schedules.start_date' , 'form_schedules.end_date')
		                ->join('form_scheduled_users' , 'form_schedules.id' , '=' , 'form_scheduled_users.schedule_id')
		                ->join('users' , 'form_scheduled_users.user_id' , '=' , 'users.id')
		                ->join('form_scheduled_forms' , 'form_scheduled_forms.schedule_id' , '=' , 'form_schedules.id')
		                ->join('forms' , 'forms.id' , '=' , 'form_scheduled_forms.form_id')
		                ->whereDate('form_schedules.start_date' , '=' , date('Y-m-d'))
		                ->get();


	        if(!empty($data) && !is_null($data)){
                    
                $formData = array();
            
	        	foreach ($data as $key => $value) {
	        	    $formData = DB::table('form_scheduled_forms')
		        	               ->select('forms.name as form_name')
		        	               ->join('forms' , 'form_scheduled_forms.form_id' , '=' , 'forms.id')
		        	               ->where('form_scheduled_forms.schedule_id' , $value->schedule_id)
		        	               ->get();

		        	if(!empty($formData) && !is_null($formData))
		        		  $data[$key]->formData = $formData;
	        	}

		        foreach($data as $key => $user){
                   $mailData['user_name']     = $user->first_name.' '.$user->last_name;
                   $mailData['user_email']    = $user->user_email;
                   $mailData['form_data']     = $user->formData;
                   $mailData['schedule_name'] = $user->schedule_name;
                   $mailData['start_date']    = $user->start_date;
                   $mailData['end_date']      = $user->end_date;
                   $mailData['template']      = 'schedule_form_mail_to_user';
                   $mailData['subject']       = 'Schedule Form';
                   if(!empty($mailData['user_email']) && !is_null($mailData['user_email']) && !empty($formData) && !is_null($formData)){ 
                     Mail::to($mailData['user_email'])->send(new NotifyMail($mailData));
                   }
                }
	        }
        }
    }
}
