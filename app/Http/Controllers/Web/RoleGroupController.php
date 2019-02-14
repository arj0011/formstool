<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\UserGroup;
use App\DataTables\UsersGroupDataTable;
use App\Role;
use DB;

class RoleGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsersGroupDataTable $dataTable,Request $request)
    {   
        $data['roles'] = Role::where([['id' , '!=' , 1],['status',1]])->get();

        return $dataTable->render('role_groups.index',compact('data'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     
     $id         = $request->id;
     $role_id    = $request->roles;
     $group_name = $request->group_name;

     // Setup the validator
     $rules = [
               'group_name'  => 'required|unique:user_groups,group_name,NULL,id,deleted_at,NULL',
      ];

     if($id){
         $rules['group_name'] = 'required|unique:user_groups,group_name,'.$id.',id,deleted_at,NULL';
     }

      $validator = Validator::make($request->all(), $rules);

      if($validator->fails()){
            return $this->backResponse('error','failed to add group',$validator->errors());
      }

       $data = [
          'group_name' => $group_name,
          'created_at' =>  \Carbon\Carbon::now(), 
          'updated_at' => \Carbon\Carbon::now()
       ];

       if(!empty($role_id) && !is_null($role_id)){
          $data['role_id'] = implode(',',$role_id);
       }else{
          $data['role_id'] = null;
       }

        if($id){
          $status = DB::table('user_groups')->where('id',$id)->update($data);
        }else{
          $status = DB::table('user_groups')->insert($data);
        }

        if($id){
            $success = 'Successfully updated group';
            $failed  = 'Failed to update group';
        }else{
            $success = 'Successfully added group';
            $failed  = 'Failed to add group';
        }

       if($status)
           return $this->backResponse('success',$success,$request->all());
       else
           return $this->backResponse('failed',$failed);
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

       $group = UserGroup::select('user_groups.id' , 'user_groups.group_name', 'user_groups.created_at','user_groups.updated_at' , 'user_groups.role_id')
                              ->where('user_groups.id',$id)
                              ->first();
       
       if(!empty($group) && !is_null($group)){
             if(!empty($group->role_id) && !is_null($group->role_id)){
                $group->role_id = explode(',', $group->role_id);
             }
       }

        if($group)
           return $this->backResponse('success','group found',$group);
       else
           return $this->backResponse('failed','group not found');

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
     public function destroy(Request $request){

       $id = $request->id;

       $status = UserGroup::where('id',$id)->delete();

       if($status){
           return $this->backResponse('success','Successfully deleted group');
       }else{
           return $this->backResponse('failed','Failed to delete group');
       }

     }

    public function backResponse($status = false , $message = 'Something went wrong' , $data = array()){

         return [
           'status'  => $status,
           'message' => $message,
           'data'    => $data
         ];
    }

    public function getGroups(Request $request){
        
        $id  = $request->id;
        $ids = $request->ids;
        
        if(!empty($id) && !is_null($id)){
          $groups      =  UserGroup::select('id' , 'group_name')->where('role_id' ,$id)->get();
        }

        if(!empty($ids) && !is_null($ids)){
          $groups      =  UserGroup::select('id' , 'group_name' , 'role_id')->whereIn('role_id' ,$ids)->get();
        }

        if(!empty($groups))
         return ['status' => true , 'message' => 'gorup found', 'data' => $groups];
       else
         return ['status' => false , 'message' => 'gorup not available found'];
 
    }

    public function status(Request $request){
        
        $id     = $request->id;
        $group   = UserGroup::find($id);
        $status = $group->status ? '0' : '1';
        $group->status = $status;
        $text =  $status ? 'Active' : 'Inactive';
        if($group->save()){
          return ['status' => true , 'message' => 'Successfully '.$text.' Group'];
        }
        else{
          return ['status' => false , 'message' => 'Failed '.$text.' Group'];
        }
 
    }
}
