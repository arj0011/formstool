<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Form;
use App\UserSchedule;
use App\FormSchedule;
use App\FormGroup;
use Auth;
use DB;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   

         $user    = Auth::user();
         $role=$user->role;
         $data=[];
         $data['users']=User::wherenull('deleted_at')->where('role' , '!=' , 1)->count();
         $data['forms']=Form::wherenull('deleted_at')->count();

         if($role==1)
         {

            $data['schedule_forms'] = FormSchedule::count();
            $data['form_groups']   = FormGroup::count();

     }else{
           $form_groups=DB::table('form_scheduled_users as fsu')
                             ->select('fg.group_id')->distinct()
                              ->where('fsu.user_id',$user->id)
                              ->leftjoin('form_scheduled_forms as sf','sf.schedule_id','=','fsu.schedule_id')
                              ->leftjoin('forms_by_groups as fg','fg.form_id','=','sf.form_id')
                              ->pluck('group_id');
                              
           $forms=DB::table('form_scheduled_users as fsu')
                             ->select('fg.form_id')->distinct()
                              ->where('fsu.user_id',$user->id)
                              ->leftjoin('form_scheduled_forms as sf','sf.schedule_id','=','fsu.schedule_id')
                              ->leftjoin('forms_by_groups as fg','fg.form_id','=','sf.form_id')
                              ->get();


           $data['schedule_forms'] =FormSchedule::join('form_scheduled_users','form_schedules.id','form_scheduled_users.schedule_id')
             ->where('form_scheduled_users.user_id', auth::id())
             ->whereDate('form_schedules.start_date', '<=' , date('Y-m-d'))
           ->count();

            $data['form_groups'] = count($form_groups);
            $data['forms']       = count($forms);  
          
         }
    return view('dashboard.dashboard', compact('data'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getNotifications(){

         $userID =auth::id();
         $notifications = UserSchedule::select('forms.id as form_id' , 'forms.form_type','forms.name' , 'form_schedules.id as schedule_id' , 'form_schedules.created_at as schedule_date' , 'form_scheduled_forms.user_readed' , 'form_schedules.start_date')
                              ->join('form_schedules' , 'form_schedules.id' , '=' , 'form_scheduled_users.schedule_id')
                              ->join('form_scheduled_forms' , 'form_scheduled_forms.schedule_id' , '=' , 'form_scheduled_users.schedule_id')
                              ->join('forms' , 'forms.id' , '=' , 'form_scheduled_forms.form_id')
                              ->where('form_scheduled_users.user_id' , $userID)
                              ->whereDate('form_schedules.start_date', '<=' , date('Y-m-d'))
                              ->get();

         if(!empty($notifications)){

            $flag = true;
            $notify = array();
            foreach ($notifications as $key => $value) {

                $notifications[$key]->start_date = date('F' , strtotime($value->start_date));
            
                if(!is_null($value->user_readed) && !empty($value->user_readed)){
                    if(in_array($userID,explode(',', $value->user_readed)))
                          $flag = false;
                      else
                          $flag = true;
                }else{
                          $flag = true;
                }

                if($flag){
                  $notifications[$key]->form_id = base64_encode($value->form_id);
                  $notifications[$key]->schedule_id = base64_encode($value->schedule_id); 
                  $notifications[$key]->schedule_date = \Carbon\Carbon::createFromTimeStamp(strtotime($value->schedule_date))->diffForHumans();
                  array_push($notify, $value);
                }
            }

             return ['status' => true , 'message' => 'found' , 'data' => $notify];
         }else{
             return ['status' => false , 'message' => 'not found' , 'data' => array()];
         }
    }
}
