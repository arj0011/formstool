<?php

namespace App\Http\Controllers\Web;

use App\DataTables\ScheduleDataTable;
use App\DataTables\ScheduleformsDataTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Auth;
use App\User;
use App\Form;
use App\Role;
use DataTables;
use App\UserGroup;
use App\FormGroup;
use App\UserSchedule;
use App\UserFormSubmit;
use App\Mail\NotifyMail;
use App\Mail\FormScheduleMail;
use App\FormSchedule;
use DateTime;
use DB;
use Mail;

class SchedulerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(ScheduleDataTable $dataTable,Request $request)
    { 
     $role=Auth::user()->role;
     if($role==1)
        {

            $data['forms']      = Form::whereNull('deleted_at')->get();
            $data['roles']      = Role::where('id' , '!=' , 1)->get();
            $data['users']      = User::select('id as user_id' , DB::raw("CONCAT(users.first_name,' ',users.last_name) as user_name"))->where('role' , '!=' , 1)->get();
          //  $data['formGroups'] = FormGroup::all();
            $data['formGroups'] = DB::table('form_groups')
                                       ->select('id' , 'group_name' , 'deleted_at' )
                                       ->get();

            $data['RoleGroup']    = UserGroup::all();

        }else{
            $data['RoleGroup']  = UserGroup::all();
            $data['formGroups'] =[];
            $data['roles']=[];
            $data['users']=[];
            if($request->has('form_id')){
                $form_id=base64_decode($request->form_id);
                $data['forms']=Form::whereNull('deleted_at')->get();
            }else $data['forms']='';
        }
      $data['authrityGroups']=UserGroup::select('id' , 'group_name' )->where('role_id' , '=' , 4)->get();

        return $dataTable->render('schedules.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  
            $schedule_name     = $request -> schedule_name;
            $schedule_for      = $request -> schedule_for;
            $roles             = $request -> roles;
            $role_groups       = $request -> role_groups;
            $forms             = $request -> forms;
            $form_groups       = $request -> form_groups;
            $start_date        = $request -> start_date;
            $end_date          = $request -> end_date;
            $specific_users    = $request -> users;

            $rules = [
                'schedule_name'    => 'required',
                'schedule_for'     => 'required',
                'form_groups'      => 'required',
                'forms'            => 'required',
                'start_date'       => 'required',
                'end_date'         => 'required',
            ];

            if($schedule_for == '_specific_role'){
                $rules['roles']            = 'required';    
            }

            if($schedule_for == '_specific_role_group'){
               $rules['role_groups']      = 'required';
            }

            $specificUserStatus = false;
            
            if($schedule_for == '_specific_user'){
                $rules['users']            = 'required';
                $specificUserStatus = true;
            }


          $validator = Validator::make($request->all(), $rules);

          if($validator->fails()){
                return [ 
                         'status'  => 'error',
                         'message' => 'invalide input',
                         'data'        => $validator->errors()
                        ];
            }

            $status                = false;
            $formsData             = array();
            $roleGroupData         = array();
            $specificUserData      = array();
            $specificRoleUsers     = array();
            $specificRoleUsersData = array();

            $scheduleData = [
              'schedule_for'   => $schedule_for,
              'start_date'     => $start_date,
              'schedule_name'  => $schedule_name,
            ];

        foreach($start_date as $key => $start_date){

            $scheduleData['start_date'] = $start_date;
            $scheduleData['end_date']   = $end_date[$key];

            $scheduleID = DB::table('form_schedules')->insertGetId($scheduleData);

            if(!empty($scheduleID)){
                
                if(!empty($forms)){
                    foreach ($forms as $key => $form) {
                        $formsData[$key]['form_id']     = $form;
                        $formsData[$key]['schedule_id'] = $scheduleID; 
                    }
                }

                if(!empty($specific_users)){
                    foreach ($specific_users as $key => $specific_user) {
                      //Get higher authority of user
                       $authorityData = array();
                       $authorityData = User::select('high_authority_user')->where('id',$specific_user)->first();
                       if(!empty($authorityData))
                           $specificUserData[$key]['authority_json'] = $authorityData['high_authority_user'];

                        $specificUserData[$key]['user_id']     = $specific_user;
                        $specificUserData[$key]['schedule_id'] = $scheduleID; 
                    }
                }

                if($schedule_for == '_specific_role'){
                  $specificRoleUsers  = User::select('users.id')
                                           ->join('roles','roles.id','users.role') 
                                           ->whereIn('users.role',$roles)
                                           ->where('users.role' , '!=' , 1)
                                           ->whereNull('roles.deleted_at')
                                           ->get();
                }

                if($schedule_for == '_specific_role_group'){
                  $specificRoleUsers  = User::select('users.id')
                                           ->join('user_groups','user_groups.id','users.group_id')  
                                           ->whereIn('users.group_id',$role_groups)
                                           ->whereNull('user_groups.deleted_at')
                                           ->get();
                }

                if($schedule_for == '_all_role'){
                  $specificRoleUsers  = User::select('users.id')
                                           ->join('roles','roles.id','users.role')
                                           ->where('role' , '!=' , 1)
                                           ->whereNull('roles.deleted_at')
                                           ->get();
                }


                if(!empty($specificRoleUsers)){
                    foreach ($specificRoleUsers as $key => $specific_role_user) {
                      //Get higher authority of user
                       $roleauthorityData = array();
                       $roleauthorityData = User::select('high_authority_user')->where('id',$specific_role_user->id)->first();
                       if(!empty($roleauthorityData))
                           $specificRoleUsersData[$key]['authority_json'] = $roleauthorityData['high_authority_user'];

                        $specificRoleUsersData[$key]['user_id']     =  $specific_role_user->id;
                        $specificRoleUsersData[$key]['schedule_id'] = $scheduleID; 
                    }
                }

                if($schedule_for == '_specific_role'){
                   
                   $rolesData = array();
                   
                   if(!empty($roles) && !is_null($roles)){
                     foreach ($roles as $key => $roleID) {
                           $rolesData[$key]['schedule_id'] = $scheduleID;
                           $rolesData[$key]['role_id']     = $roleID; 
                     }
                   }

                   if(!empty($rolesData) && !is_null($rolesData)){
                     DB::table('schedule_roles')->insert($rolesData);
                   }

                }

                if($schedule_for == '_specific_role_group'){
                   
                   $roleGroupsData = array();
                   
                   if(!empty($role_groups) && !is_null($role_groups)){
                     foreach ($role_groups as $key => $roleGroupID) {
                           $roleGroupsData[$key]['schedule_id'] = $scheduleID;
                           $roleGroupsData[$key]['role_group_id']    = $roleGroupID; 
                     }
                   }

                   if(!empty($roleGroupsData) && !is_null($roleGroupsData)){
                     DB::table('schedule_role_groups')->insert($roleGroupsData);
                   }

                }


                // if($schedule_for == '_specific_role_group'){
                //     DB::table('form_scheduled_forms')->insert($formsData);
                // }
                    
                DB::table('form_scheduled_forms')->insert($formsData);

                if($specificUserStatus){
                       DB::table('form_scheduled_users')->insert($specificUserData);
                }else{
                   if(!empty($specificRoleUsersData)){
                       DB::table('form_scheduled_users')->insert($specificRoleUsersData);
                     }
                }

               // send emal to user
                if(strtotime($start_date) == strtotime(date('Y-m-d'))){
                
                 $userData = array();

                 if(!empty($specificRoleUsers) && !is_null($specificRoleUsers)){
                    $userData =  DB::table('users')->select('email' , 'first_name' , 'last_name')->whereIn('id' , $specificRoleUsers)->get();
                 }

                 if(!empty($specific_users) && !is_null($specific_users)){
                    $userData = DB::table('users')->select('email' , 'first_name' , 'last_name')->whereIn('id' , $specific_users)->get();
                 }


                $formsData =  DB::table('forms')->select('name as form_name')->whereIn('id',$forms)->get();


                  if(!empty($formsData) && !is_null($formsData) && !empty($userData) && !is_null($userData)){
         
                    foreach($userData as $key => $user){
                       $mailData['user_name']     = $user->first_name.' '.$user->last_name;
                       $mailData['user_email']    = $user->email;
                       $mailData['form_data']     = $formsData;
                       $mailData['schedule_name'] = $schedule_name;
                       $mailData['start_date']    = $start_date;
                       $mailData['end_date']      = $scheduleData['end_date'];
                       $mailData['template']      = 'schedule_form_mail_to_user';
                       $mailData['subject']       = 'Schedule Form';

                       if(!empty($mailData['user_email']) && !is_null($mailData['user_email'])){ 
                         Mail::to($mailData['user_email'])->send(new NotifyMail($mailData));
                       }
                    }
                  }   
                }
              // End email functionality
            }
        }

            $status = true;

            if($status){
           
              return ['status'  => 'success','message' => 'successfully schedule forms'];
                
            }else
              return ['status'  => 'failed', 'message' => 'failed to schedule forms'];
            
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $id = $request->id;

        if(!empty($id) && !is_null($id)){

                $scheduleData  = DB::table('form_schedules')
                                ->select('start_date' , 'schedule_for' , 'created_at as schedule_date' , 'end_date' , 'schedule_name')
                                ->where('id',$id)
                                ->first();

                if(!empty($scheduleData)){

                     $data['schedule_data'] = $scheduleData;

                     $data['forms'] = DB::table('form_scheduled_forms')
                                    ->select('forms.id' , 'forms.name')
                                    ->join('forms' , 'forms.id' , '=' , 'form_scheduled_forms.form_id')
                                    ->where('schedule_id',$id)
                                    ->orderBy('forms.name' , 'ASC')
                                    ->get();
                     // DB::raw("(CASE WHEN submit.submit_status = 1 THEN 'Submited' ELSE 'Pending' END) AS submit_status")
                     $data['users']=DB::table('form_scheduled_users')
                                    ->select('users.id' , 'users.first_name' , 'users.last_name','users.high_authority_user' , 'user_groups.group_name' , 'forms.name as form_name' , 'submit.submit_status', 'user_groups.deleted_at as group_deleted_at')
                                    ->join('users' , 'users.id' , '=' , 'form_scheduled_users.user_id')
                                    ->leftJoin('user_groups' , 'user_groups.id' , '=' , 'users.group_id')
                                    ->join('form_schedules' , 'form_schedules.id' , '=' , 'form_scheduled_users.schedule_id')
                                    ->join('form_scheduled_forms' , 'form_scheduled_forms.schedule_id' , '=' , 'form_schedules.id')
                                    ->join('forms' , 'forms.id' , '=' , 'form_scheduled_forms.form_id')
                                    ->leftJoin('user_form_submitted AS submit', function($join){
                                        $join->on('forms.id', '=', 'submit.form_id')
                                             ->on('users.id', '=', 'submit.user_id')
                                             ->on('form_schedules.id', '=', 'submit.schedule_id');
                                    })
                                    ->where('form_scheduled_users.schedule_id',$id)
                                    ->groupBy('forms.id','users.id')
                                    ->orderBy('users.first_name' , 'ASC')
                                    ->get();

                    return view('schedules.schedule',compact('data'));

                }
           
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->id;

        if(!empty($id) && !is_null($id)){

                $scheduleData  = DB::table('form_schedules')
                                ->select('start_date' , 'schedule_for' , 'end_date' , 'schedule_name')
                                ->where('id',$id)
                                ->first();

                if(!empty($scheduleData)){

                     $data['schedule_data'] = $scheduleData;

                     $data['form_and_group_ids'] = DB::table('form_scheduled_forms')
                                    ->select('form_scheduled_forms.form_id','form_groups.id as group_id' , 'form_groups.group_name')
                                    ->join('forms_by_groups' , 'forms_by_groups.form_id' , '=' , 'form_scheduled_forms.form_id')
                                    ->join('form_groups' , 'form_groups.id' , '=' , 'forms_by_groups.group_id')
                                    ->where('form_scheduled_forms.schedule_id',$id)
                                    ->groupBy('form_groups.id')
                                    ->get();
                      $groupIds = array();

                     if(!is_null($data['form_and_group_ids']) && !empty($data['form_and_group_ids'])){
                            foreach ($data['form_and_group_ids'] as $key => $value) {
                                 array_push($groupIds, $value->group_id);
                            }

                            if(!empty($groupIds)){
                                 $data['form_groups'] = DB::table('form_groups')
                                                             ->select('id as group_id','group_name')
                                                             ->where('deleted_at' , '=' , null)
                                                             ->whereNotIn('id',$groupIds)
                                                             ->get();
                            }
                     }

                    $data['role_and_group_ids'] = DB::table('form_scheduled_users')
                                    ->select('users.role as role_id' , 'user_groups.id as group_id')
                                    ->join('users' , 'users.id' , '=' , 'form_scheduled_users.user_id')
                                    ->join('user_groups' , 'user_groups.id' , '=' , 'users.group_id')
                                    ->where('form_scheduled_users.schedule_id',$id)
                                    ->distinct('user_groups.id')
                                    ->get();

                    $data['roles']        = DB::table('schedule_roles')
                                           ->select('role_id')
                                           ->where('schedule_id' , $id)
                                           ->distinct('role_id')
                                           ->get();

                    $data['role_groups']  = DB::table('schedule_role_groups')
                                           ->select('role_group_id')
                                           ->where('schedule_id' , $id)
                                           ->distinct('role_group_id')
                                           ->get();


                    $data['user_ids'] = array();
                     if($data['schedule_data']->schedule_for == '_specific_user'){
                         $data['user_ids'] = DB::table('form_scheduled_users')
                                        ->select('user_id')
                                        ->where('schedule_id',$id)
                                        ->get();
                     }
                    
                    return [
                      'status'  => true,
                      'message' => 'success',
                      'data'    => $data
                     ];

                }

                
               return [
                 'status'  => false,
                 'message' => 'something went wrong',
                ];

        }

        return [
         'status'  => false,
         'message' => 'something went wrong',
        ];

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {      

            $schedule_name     = $request -> schedule_name;
            $schedule_id       = $request -> id;
            $schedule_for      = $request -> schedule_for;
            $form_groups       = $request -> form_groups;
            $forms             = $request -> forms;
            $roles             = $request -> roles;
            $role_groups       = $request -> role_groups;
            $start_date        = $request -> start_date;
            $specific_users    = $request -> users;
            $end_date          = $request -> end_date;

            $rules = [
                'schedule_name'    => 'required',
                'schedule_for'     => 'required',
                'form_groups'      => 'required',
                'forms'            => 'required',
                'start_date'       => 'required',
                'end_date'         => 'required',
            ];

            if($schedule_for == '_specific_role'){
                $rules['roles']            = 'required';
            }

            if($schedule_for == '_specific_role_group'){
               $rules['role_groups']      = 'required';
            }

            $specificUserStatus = false;
            
            if($schedule_for == '_specific_user'){
                $rules['users']            = 'required';
                $specificUserStatus = true;
            }

            $validator = Validator::make($request->all(), $rules);

          if($validator->fails()){
                return [ 
                         'status'  => 'error',
                         'message' => 'invalide input',
                         'data'        => $validator->errors()
                        ];
            }

            $status                = false;
            $formsData             = array();
            $roleGroupData         = array();
            $specificUserData      = array();
            $specificRoleUsers     = array();
            $specificRoleUsersData = array();
          //  $start_date = DateTime::createFromFormat('d/m/Y', $start_date)->format('Y-m-d');

            $scheduleData = [
              'schedule_for'   => $schedule_for,
              'start_date'     => $start_date,
              'end_date'       => $end_date,
              'schedule_name'  => $schedule_name,
            ];

        foreach($start_date as $key => $start_date){

            $scheduleData['start_date'] = $start_date;
            $scheduleData['end_date']   = $end_date[$key];

            DB::table('form_schedules')->where('id' , $schedule_id)->update($scheduleData);
            $scheduleID = $schedule_id;

            if(!empty($scheduleID)){
                
                if(!empty($forms)){
                    foreach ($forms as $key => $form) {
                        $formsData[$key]['form_id']     = $form;
                        $formsData[$key]['schedule_id'] = $scheduleID; 
                    }
                }

                if(!empty($specific_users)){
                    foreach ($specific_users as $key => $specific_user) {
                        
                       //Get higher authority of user
                       $authorityData = array();
                       $authorityData = User::select('high_authority_user')->where('id',$specific_user)->first();
                       if(!empty($authorityData))
                           $specificUserData[$key]['authority_json'] = $authorityData['high_authority_user'];

                        $specificUserData[$key]['user_id']     = $specific_user;
                        $specificUserData[$key]['schedule_id'] = $scheduleID; 
                    }
                }

                if($schedule_for == '_specific_role'){
                  $specificRoleUsers  = User::select('users.id')
                                           ->join('roles','roles.id','users.role') 
                                           ->whereIn('users.role',$roles)
                                           ->where('users.role' , '!=' , 1)
                                           ->whereNull('roles.deleted_at')
                                           ->get();
                }

                if($schedule_for == '_specific_role_group'){
                  $specificRoleUsers  = User::select('users.id')
                                           ->join('user_groups','user_groups.id','users.group_id')  
                                           ->whereIn('users.group_id',$role_groups)
                                           ->whereNull('user_groups.deleted_at')
                                           ->get();
                }

                if($schedule_for == '_all_role'){
                  $specificRoleUsers  = User::select('users.id')
                                           ->join('roles','roles.id','users.role')
                                           ->where('role' , '!=' , 1)
                                           ->whereNull('roles.deleted_at')
                                           ->get();
                }

                     DB::table('schedule_roles')->where('schedule_id' , $schedule_id)->delete();
                     DB::table('schedule_role_groups')->where('schedule_id' , $schedule_id)->delete();

                if($schedule_for == '_specific_role'){
                   
                   $rolesData = array();
                   
                   if(!empty($roles) && !is_null($roles)){
                     foreach ($roles as $key => $roleID) {
                           $rolesData[$key]['schedule_id'] = $scheduleID;
                           $rolesData[$key]['role_id']     = $roleID; 
                     }
                   }

                   if(!empty($rolesData) && !is_null($rolesData)){
                     DB::table('schedule_roles')->insert($rolesData);
                   }

                }

                if($schedule_for == '_specific_role_group'){
                   
                   $roleGroupsData = array();
                   
                   if(!empty($role_groups) && !is_null($role_groups)){
                     foreach ($role_groups as $key => $roleGroupID) {
                           $roleGroupsData[$key]['schedule_id'] = $scheduleID;
                           $roleGroupsData[$key]['role_group_id']    = $roleGroupID; 
                     }
                   }

                   if(!empty($roleGroupsData) && !is_null($roleGroupsData)){
                     DB::table('schedule_role_groups')->insert($roleGroupsData);
                   }

                }

                if(!empty($specificRoleUsers)){
                    foreach ($specificRoleUsers as $key => $specific_role_user) {
                      //Get higher authority of user
                       $roleauthorityData = array();
                       $roleauthorityData = User::select('high_authority_user')->where('id',$specific_role_user->id)->first();
                       if(!empty($roleauthorityData))
                           $specificRoleUsersData[$key]['authority_json'] = $roleauthorityData['high_authority_user'];

                        $specificRoleUsersData[$key]['user_id']     =  $specific_role_user->id;
                        $specificRoleUsersData[$key]['schedule_id'] = $scheduleID; 
                    }
                }

                       DB::table('form_scheduled_forms')->where('schedule_id' , $schedule_id)->delete();
                       DB::table('form_scheduled_forms')->insert($formsData);

                if($specificUserStatus){
                       DB::table('form_scheduled_users')->where('schedule_id' , $schedule_id)->delete();
                       DB::table('form_scheduled_users')->insert($specificUserData);
                }else{
                   if(!empty($specificRoleUsersData)){
                       DB::table('form_scheduled_users')->where('schedule_id' , $schedule_id)->delete();
                       DB::table('form_scheduled_users')->insert($specificRoleUsersData);
                     }
                }

            }
        }

               $status = true;

            if($status)
              return ['status'  => 'success','message' => 'successfully updated'];
            else
              return ['status'  => 'failed', 'message' => 'failed to update'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
         $id = $request->id;

         if(!empty($id) && !is_null($id)){

             $FormSchedule = FormSchedule::find($id);
            
             if($FormSchedule->delete()){
                   $arr = array('status' => true , 'message' => 'Successfully deleted schedule');
                   return response($arr);
             }else{
                  $arr = array('status' => false , 'message' => 'Failed to delete schedule');
                   return response($arr);
             }

         }

         return ['status' => false , 'message' => 'Something went wrong'];
    }

   public function getForms(Request $request){
         
         $ids     = $request->ids;
         $dataFor = $request->dataFor;

         $forms = DB::table('forms_by_groups')
                       ->select('forms.id','forms.name','forms.form_type','forms_by_groups.group_id')
                       ->join('forms' , 'forms.id' , '=' , 'forms_by_groups.form_id')
                       ->whereIn('forms_by_groups.group_id' , $ids)
                       ->where(function($query) use ($dataFor) {
                            if($dataFor == 'create'){
                                 $query->whereNull('deleted_at');
                            }
                       })
                       ->get();

          if(!empty($forms->toArray()) && !is_null($forms->toArray())){
         
           foreach ($forms as $key => $form) {
             $form_name = null;
             if(strtolower(trim($form->form_type)) == strtolower('Tabular')){
               $table_id =  DB::table('tables')->select('id as table_id')->where('form_id' , $form->id)->first();
               if(!empty($table_id) && !is_null($table_id)){
                 $form_name = str_replace(' ', '_', strtolower($form->name)).'_'.$form->id.'_'.$table_id->table_id;
               }
             }else{
               $form_name = str_replace(' ', '_', strtolower($form->name)).'_'.$form->id;
             }
             
             if($form_name){
               if(!Schema::hasTable($form_name)){
                   unset($forms[$key]);
               } 
             }else{
                 unset($forms[$key]);
             }
           }
         }

         $formss = array();

         if(!empty($forms)){
           foreach($forms as $form){
            $formss[] = $form;
           }
         }
         
         if(!empty($formss)){
             return ['status' => true , 'message' => 'form found' , 'data' => $formss];
         }else{
             return ['status' => false , 'message' => 'form not found' , 'data' => $formss];
         }
    }

    /**
    * Cron    : http://localhost/formstool/cron/schedule-authority-email
    * Purpose : To sent email to higher authority of scheduled user in case of not
    *        submitting schedule form.    
    */
    
    public function scheduleAuthorityEmail()
    {
        
        $forms = FormSchedule::select('id','end_date','schedule_name')->whereDate('end_date', '<=', date('Y-m-d'))->get();
        if($forms){
            foreach($forms as $form){
                $scheduleUser = UserSchedule::select('user_id')->where('schedule_id',$form->id)->get();   
                if($scheduleUser){
                    $firstArr = $scheduleUser->toArray();
                    $firstArr = array_column($firstArr, 'user_id');
                    $secondObj = UserFormSubmit::select('user_id')->where([['schedule_id', '=', $form->id],['submit_status','=','1']])->get(); 
                    if($secondObj){
                        $secondArr = $secondObj->toArray();
                        $secondArr = array_column($secondArr, 'user_id');
                        $firstArr = array_diff($firstArr,$secondArr);       
                    }
                    if(!empty($firstArr)){
                        foreach($firstArr as $user){
                
                            $userdata = UserSchedule::select(DB::raw('concat(users.first_name," ",users.last_name) as user_name'),'form_scheduled_users.authority_json','form_scheduled_users.is_email_sent')
                            ->leftJoin('users', function($join) {
                                $join->on('form_scheduled_users.user_id', '=', 'users.id');
                            })
                            ->where([['user_id','=',$user],['schedule_id','=',$form->id]])->first();
                            if(!empty($userdata)){
                                $authority_json = json_decode($userdata['authority_json'],true);
                                $data = array(
                                        'form_name'  => $form->schedule_name,
                                        'user_name'  => $userdata['user_name'],
                                        'end_date'   => $form->end_date,
                                        'template'   => 'schedule_authority_mail',
                                        'subject'    => 'Schedule form not submitted'
                                    );
                                    
                                
                                if(is_null($userdata['is_email_sent'])){               
                                    $email = $authority_json[0]['email'];
                                    //send email
                                    $data['user_email'] = $email;

                                    if(!empty($data['user_email']) && !is_null($data['user_email'])){
                                      Mail::to($data['user_email'])->send(new NotifyMail($data));

                                      //update schedule user
                                      $updatedata['is_email_sent'] = 1;
                                      UserSchedule::where([['user_id','=',$user],['schedule_id','=',$form->id]])
                                              ->update($updatedata);
                                    }
                                }else{
                                    $prioty = explode(',',$userdata['is_email_sent']);
                                    $ind = end($prioty);
                                    $nxt = $ind + 1;
                                    foreach ($authority_json as $key => $value) {
                                        if($value['priority'] == $nxt){
                                            $data['user_email'] = $value['email'];

                                            //send email
                                          if(!empty($data['user_email']) && !is_null($data['user_email'])){
                                            Mail::to($data['user_email'])->send(new NotifyMail($data));        
                                            //update schedule user
                                            $updatedata['is_email_sent'] = $userdata['is_email_sent'].','.$nxt;
                                            UserSchedule::where([['user_id','=',$user],['schedule_id','=',$form->id]])
                                            ->update($updatedata);
                                            break;
                                          }
                                        }
                                    }
                                }
                            }
                        }
                    } 
                }
            }
        }
    }

    public function scheduleForms(ScheduleformsDataTable $dataTable,Request $request)
    {
      $scheduleData = FormSchedule::select('schedule_name')->where('id',base64_decode($request->schedule_id))->first();
      if($scheduleData)
        $data['schedule_name'] = $scheduleData->schedule_name;
      return $dataTable->render('schedules.scheduleforms',compact('data'));
    }

}
