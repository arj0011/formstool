<?php

namespace App\Http\Controllers\Web;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Hash;
use App\Mail\Registration;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\UserSchedule;
use App\UserGroup;
use Mail;
use App\DataTables\UsersDataTable;
use App\DataTables\UsersReportDataTable;
use App\Exports\UsersReportExport;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;


class UserController extends Controller
{
    public function index(UsersDataTable $dataTable,Request $request){

       $data['role']   = $request->role;

       $data['group']  = $request->group;

       $data['districs'] = DB::table('districs')->get();

       $data['roles']  = Role::select('id','name')->where('id' , '!=' , 1)->get();
 
       $data['groups'] = UserGroup::select('id','group_name')->get();
    	
       return $dataTable->render('users.index',compact('data'));
    }

    public function edit(Request $request){
    
    	  $id = $request->id;

        $data['user'] = User::select('users.id','users.first_name' , 'users.last_name' , 'users.email' , 'users.mobile' , 'users.gender' , 'users.status' , 'users.profile_image' , 'role' ,'users.group_id as group','high_authority_user' , 'users.distric_id')
                              ->where('id' , $id)
                              ->first();

        $data['districs'] = DB::table('districs')->get();

        $data['roles'] = Role::select('id' , 'name')->where('id' , '!=' , 1 )->get();

        $data['groups'] = UserGroup::select('id','group_name')->get();

        if(!empty($data['user'])){
          if(empty($data['user']->profile_image) || is_null($data['user']->profile_image) || !file_exists('public/images/profile_images/'.$data['user']->profile_image))
             $data['user']->profile_image = 'image_not_available.jpeg';
        }

        return view('users.add',compact('data'));

    }

    public function create(){

      $data['districs'] = DB::table('districs')->get();
      
      $data['roles'] = Role::select('id' , 'name')->where('id' , '!=' , 1)->get();

      $data['groups'] = UserGroup::select('id','group_name')->get();

      return view('users.add',compact('data'));
    }

    public function store(Request $request){

    	$first_name           =   $request->first_name;
   	    $last_name          =   $request->last_name;
   	    $mobile             =   $request->mobile;
   	    $gender             =   $request->gender;
   	    $profile_image      =   $request->profile_image;
        $email              =   $request->email;
        $role               =   $request->role;
        $password           =   $request->password;
        $confirmPassword    =   $request->confirm_password;
        $notify             =   $request->notify;
        $group              =   $request->group;
        $highAuthorityUsers =   $request->high_authority_user;
        $distric            =   $request->distric;
   
        $rules = array(
            'first_name'    => 'required|min:2|max:50',
            'last_name'     => 'required|min:2|max:50',
            'mobile'        => 'required|unique:users,mobile,NULL,id,deleted_at,NULL',
            'email'         => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'gender'        => 'required|in:Male,Female',
            'profile_image' => 'image|mimes:jpeg,jpg,png',
            'password'      => 'min:6|required_with:confirm_password|same:confirm_password',
            'confirm_password'  => 'required|min:6',
            'high_authority_user' => 'required',
            'group'               => 'required',
            'distric'             => 'required',
            'role'               => 'required'
          );


      //   $rules['high_authority_user.*'] = 'required';


      // if(!empty($highAuthorityUsers[0]) && !is_null($highAuthorityUsers[0])){
      //      $rules['high_authority_user.*'] = 'email|distinct';
      // }

    	$validatedData = $request->validate($rules);

      $image = '';
      if ($request->hasFile('profile_image')) {
          $profileImage = str_random('10').'.'.time().'.'.request()->profile_image->getClientOriginalExtension();
          request()->profile_image->move(public_path('images/profile_images'), $profileImage);
          $image = $profileImage;
      }

        $authority = array();
        
        if(!empty($highAuthorityUsers) && !is_null($highAuthorityUsers)){
          foreach ($highAuthorityUsers as $key => $value) {
            if(!empty($value) && !is_null($value)){
               $authority[] = array('email'=>$value,'priority' => ++$key );
            }
          }
        }

	    $user = new User;
			$user->first_name = $first_name;
			$user->last_name  = $last_name;
			$user->mobile     = $mobile;
		 	$user->gender     = $gender;
      $user->email      = $email;
      $user->password   = Hash::make($password);
      $user->distric_id    = $distric;
      $user->high_authority_user = json_encode($authority);
      if(!empty($group)){ $user->group_id = $group ;}
		 	if(!empty($image)){$user->profile_image  = $image;}
      if(!empty($role)){$user->role  = $role;}

  		if($user->save()){

          $role = Role::find($role);

          $data = array(
              'name'     => $first_name.' '.$last_name,
              'role'     => $role->name,
              'email'    => $email,
              'mobile'   => $mobile,
              'password' => $password,
          );
          
          
          if(!empty($notify)){
               Mail::to($email)->send(new Registration($data));
          }

            return $this->returnResponse(true,'Successfully Added User', 'users');
            
  		}else{

  			if(!empty($image) && file_exists('public/images/profile_images/'.$image)){
  				unlink('public/images/profile_images/'.$image);
  			}
            return $this->returnResponse(false,'Failed To Add User', 'users');
  		}
    }

     public function update(Request $request){
        
      $id = $request->id;

    	$first_name         =  $request ->first_name;
   	  $last_name          =  $request ->last_name;
   	  $mobile             =  trim($request ->mobile);
   	  $gender             =  $request ->gender;
      $email              =  trim($request ->email);
   	  $profile_image      =  $request ->profile_image;
      $role               =  $request ->role;
      $group              =  $request ->group;
      $highAuthorityUsers =  $request->high_authority_user;
      $distric            =  $request->distric;

      $rules = array(
        'first_name'    => 'required|min:2|max:50',
        'last_name'     => 'required|min:2|max:50',
        'mobile'        => 'required|unique:users,mobile,'.$id.',id,deleted_at,NULL',
        'gender'        => 'required',
        'email'         => 'required|unique:users,email,'.$id.',id,deleted_at,NULL',
        'profile_image' => 'image|mimes:jpeg,jpg,png',
        'group'               => 'required',
        'distric'             => 'required',
        'role'                => 'required'
      );
        
      // $rules['high_authority_user.*'] = 'required';


      // if(!empty($highAuthorityUsers[0]) && !is_null($highAuthorityUsers[0])){
      //      $rules['high_authority_user.*'] = 'email|distinct';
      // }
      
      $validatedData   = $request->validate($rules);
       
	   	$old_image = User::select('profile_image')->where('id' , $id)->first();

      $image = '';

      if ($request->hasFile('profile_image')) {
          $profileImage = str_random('10').'.'.time().'.'.request()->profile_image->getClientOriginalExtension();
          request()->profile_image->move(public_path('images/profile_images'), $profileImage);
          $image = $profileImage;
      }
       $authority = array();
       if(!empty($highAuthorityUsers) && !is_null($highAuthorityUsers)){
          foreach ($highAuthorityUsers as $key => $value) {
              if(!empty($value) && !is_null($value)){
                    $authority[] = array('email'=>$value,'priority' => ++$key);
              }
          }
        }

		$user = User::find($id);
				$user->first_name = $first_name;
				$user->last_name  = $last_name;
				$user->mobile     = $mobile;
			 	$user->gender     = $gender;
        $user->email      = $email;
        $user->distric_id    = $distric;
        $user->high_authority_user = json_encode($authority);
        if(!empty($group)){ $user->group_id = $group ;}
			 	if(!empty($image)){$user->profile_image  = $image;}
        if(!empty($role)){$user->role  = $role;}

		if($user->save()){

        $scheduleStatus = DB::table('form_scheduled_users')->where('user_id',$id)->count();

        if($scheduleStatus > 0){

          DB::table('form_scheduled_users')->where('user_id',$id)->update(['authority_json' => json_encode($authority)]);
        }

        return $this->returnResponse(true,'Successfully Updated User', 'users');
        
        if(!empty($image) && file_exists('public/images/profile_images/'.$old_image->profile_image)){
				  unlink('public/images/profile_images/'.$old_image->profile_image);
			
      }
		}else{
			if(!empty($image) && file_exists('public/images/profile_images/'.$image)){
				unlink('public/images/profile_images/'.$image);
			}
          return $this->returnResponse(false,'Failed To Update User', 'users');
		}
    }

    public function destroy(Request $request){
       $id = $request->id;
       $user = User::where('id',$id)->delete();
       if($user){
           $arr = array('status' => true , 'message' => 'Successfully deleted user');
           return response($arr);
       }else{
          $arr = array('status' => false , 'message' => 'Failed to delete user');
           return response($arr);
       }

    }

    public function show(Request $request){
          
          $id = $request->id;

          $data['user'] = User::select('users.id' , 'users.first_name' , 'users.last_name' , 'users.email' , 'users.mobile' , 'users.status' , 'users.gender' , 'users.profile_image' , 'users.role' , 'users.high_authority_user' , 'districs.distric','user_groups.group_name' , 'user_groups.deleted_at as group_deleted_at')->leftJoin('districs' , 'users.distric_id' , '=' , 'districs.id')->leftJoin('user_groups' , 'users.group_id' , '=' , 'user_groups.id')->where('users.id' , $id )->first();
          
          if(!empty($data['user']->role) && !is_null($data['user']->role)){
                $role = Role::find($data['user']->role);
                if(!empty($role) && !is_null($role)){
                  $data['user']->role = $role->name;
                }else{
                  $data['user']->role = 'N/A';
                }
          }else{
               $data['user']->role = 'N/A';
          }


          if(!empty($data['user']->high_authority_user) && !is_null($data['user']->high_authority_user)){
                $data['user']->high_authority_users = json_decode($data['user']->high_authority_user);
                unset($data['user']->high_authority_user);
          }else{
                $data['user']->high_authority_users = array();
          }

          if(!empty($data['user'])){
            if(empty($data['user']->profile_image) || is_null($data['user']->profile_image) || !file_exists('public/images/profile_images/'.$data['user']->profile_image))
            $data['user']->profile_image = 'image_not_available.jpeg';
          }

          return view('users.user' , compact('data'));

    }

     public function status(Request $request){
        
        $id     = $request->id;
        $user   = User::find($id);
        $status = $user->status ? '0' : '1';
        $user->status = $status;
        $text =  $status ? 'Active' : 'Inactive';
        if($user->save()){
          return ['status' => true , 'message' => 'Successfully '.$text.' user'];
        }
        else{
          return ['status' => false , 'message' => 'Failed '.$text.' user'];
        }
 
    }

    public function returnResponse($status , $message , $redirect ){
       
       return redirect($redirect)->with('msg' , $message)->with('status' , $status );

    }

    public function getGroupUsers(Request $request){

        $ids  = $request->ids;

        $groupUsers   = User::select('id' , 'first_name' , 'last_name' , 'group_id')
                               ->whereIn('group_id',$ids)
                               ->get();

        if(!empty($groupUsers))
         return ['status' => true , 'message' => 'data found', 'data' => $groupUsers];
       else
         return ['status' => false , 'message' => 'data not available found'];
    }

    public function getUserReport(UsersReportDataTable $dataTable,Request $request)
    {

       $data['role'] = $request->role;
       $data['group'] = $request->group;

       $data['roles'] = Role::select('id','name')->where('id' , '!=' , 1)->get();
       $data['groups'] = UserGroup::select('id','group_name')->get();
      
       return $dataTable->render('users.user_report',compact('data'));
    }


    public function exportUserReport(Request $request)
    {
      // $model = new UserSchedule; 
      // $data = $model->newQuery()
        $data = UserSchedule::select(
          'users.first_name as user_name','users.last_name',
          'user_groups.group_name as group','roles.name as role' , 'districs.distric','form_schedules.schedule_name','form_schedules.start_date',
          'form_schedules.end_date','submit.submit_status',
          'submit.record_accept_status', 'roles.deleted_at as role_deleted_at' , 'user_groups.deleted_at as group_deleted_at' ,
          DB::raw('(CASE WHEN submit.submitted_date IS NOT NULL THEN submit.submitted_date ELSE form_schedules.created_at END) AS created_date'))
                    ->join('users' , 'users.id' , '=' , 'form_scheduled_users.user_id')
                    ->leftJoin('roles','users.role','roles.id')
                    ->leftJoin('user_groups' , 'user_groups.id' , '=' , 'users.group_id')
                    ->leftJoin('districs' , 'users.distric_id' , '=' , 'districs.id')
                    ->join('form_schedules' , 'form_schedules.id' , '=' , 'form_scheduled_users.schedule_id')
                    ->join('form_scheduled_forms' , 'form_scheduled_forms.schedule_id' , '=' , 'form_schedules.id')
                    ->join('forms' , 'forms.id' , '=' , 'form_scheduled_forms.form_id')
                    ->leftJoin('user_form_submitted AS submit', function($join){
                        $join->on('forms.id', '=', 'submit.form_id')
                            ->on('users.id', '=', 'submit.user_id')
                            ->on('form_schedules.id', '=', 'submit.schedule_id');
                        });
                    if($request->role){
                      $data->where('users.role',$request->role);
                    }
                    if($request->group){
                        $data->where('users.group_id',$request->group);
                    }
                    $data->orderBy('created_date','desc');    
                    $datat = $data->get();

                    if(!empty($datat) && !is_null($datat)){
                      foreach ($datat as $key => $value) {
                        if(!is_null($value->role_deleted_at))
                          $datat[$key]->role = '-' ;
                      }

                      foreach ($datat as $key => $value) {
                        if(!is_null($value->group_deleted_at))
                          $datat[$key]->group = '-' ;
                      }
                    }

      $fileName = ($request->type == 'xls') ? 'UserReportExcel' : 'UserReport'.ucfirst($request->type);              
      return Excel::download(new UsersReportExport($datat),$fileName.'.'.$request->type);
    }

    function exportUsers(Request $request){

       $role  = $request->role;
       $group = $request->group;

       $data = User::select('users.first_name' , 'users.last_name' , 'users.gender' ,'users.mobile' , 'users.email' , 'groups.group_name' , 'roles.name as role_name' , 'districs.distric'  , 'users.created_at as registration_data' , 'roles.deleted_at as role_deleted_at' , 'groups.deleted_at as group_deleted_at')
                    ->where('users.role' , '!=' , 1)
                    ->wherenull('users.deleted_at')
                    ->leftJoin('roles','users.role','roles.id')
                    ->leftJoin('districs' , 'users.distric_id' , '=' , 'districs.id')
                    ->leftJoin('user_groups as groups' , 'groups.id' , '=' , 'users.group_id');

                    if(!empty($role) && !is_null($role)){
                       $data->where('users.role' , $role);
                    }

                    if(!empty($group) && !is_null($group)){
                       $data->where('users.group_id' , $group);
                    }
                        
      $data = $data->orderBy('users.id' , 'DESC')->get();

       if(!empty($data) && !is_null($data)){
          foreach ($data as $key => $value) {
            if(!is_null($value->role_deleted_at))
              $data[$key]->role_name = '-' ;
          }

          foreach ($data as $key => $value) {
            if(!is_null($value->group_deleted_at))
              $data[$key]->group_name = '-' ;
          }
       }

      $fileName = ($request->type == 'xls') ? 'UserExcel' : 'UserReport'.ucfirst($request->type);              
      return Excel::download(new UsersExport($data),$fileName.'.'.$request->type);
    }

     public function getRolesByGroup(Request $request){
        
       $id  = $request->id;

       $roles = array();
       
       if(!empty($id) && !is_null($id)){
          $groups      =  DB::table('user_groups')
                                     ->select('role_id')
                                     ->where('id' ,$id)
                                     ->whereNull('deleted_at')
                                     ->first();
          
          if(!empty($groups) && !is_null($groups)){
             $role_id   = explode(',', $groups->role_id);

             if($role_id){
                $roles     =  DB::table('roles')
                                     ->select('id' , 'name')
                                     ->whereIn('id' ,$role_id)
                                     ->whereNull('deleted_at')
                                   //  ->where('status',1)
                                     ->get();
                                     
             }
          }
        }

        if(!empty($roles))
         return ['status' => true , 'message' => 'gorup found', 'data' => $roles];
       else
         return ['status' => false , 'message' => 'gorup not available'];
 
    }
   
}
