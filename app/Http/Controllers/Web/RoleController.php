<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DataTables;
use  App\User;
use  App\Role;
use  App\Module;
use  App\Permission;
use DB;

class RoleController extends Controller
{
    
    function index(Request $request)
    {
       $this->authorize('index', Role::class);

    	return view('role/index');
    }

    function ajax_Role()
    {  
       $this->authorize('index', Role::class);

       $roles=Role::where('id' , '!=' , 1)->get();

       if($roles->count()<=0) $roles=[];
    
       return DataTables::of($roles)->make(true);
    }

    function create(Request $request)
    {  
        
        $this->authorize('create', Role::class);

        $data['modules'] = Module::select( 'id' , 'module_name')->orderBy('module_name', 'ASC')->get();
        return view("role/add", compact('data'));

    }

    function delete($id)
    {
      
      $this->authorize('delete', Role::class);

        $role=Role::find($id)->delete(); 
       if($role)
        {
            $response=['status'=>1,
                'message'=>'Role deleted Successfully!'
                   ];
            return response()->json($response);
        }
        else{
            $response=['status'=>0,
                   'message'=>'Something went wrong!'
                   ];
                 return response()->json($response);
            }
    }
function store(Request $request)
{
    $this->authorize('create', Role::class);

    $role        = $request->role;
    $permissions = $request->permissions;

    $request->validate([
        'role'    => 'required|min:2|max:50|unique:roles,name,NULL,id,deleted_at,NULL',
    ]);
       
      $role = new Role;
      $role->name = $request->role;
      $role->save();

      if(!empty($permissions)){
        $role->permissions()->sync($permissions);
      }

       if($role->id){
          return $this->returnResponse(true , 'Successfully Created Role' , 'index');
        }
        else{
          return $this->returnResponse(false , 'Failed to create role' , 'index');
        }
}
function edit($id=''){
   
   $this->authorize('update', Role::class);

    if(!empty($id))
    {   

      $id=base64_decode($id);

      $data['role']             = Role::find($id);

      $data['modules']          = Module::select( 'id' , 'module_name')->orderBy('module_name', 'ASC')->get();

      return view('role.add',compact('data'));

    }else return back();
    
}

function update(Request $request)
{

    $this->authorize('update', Role::class);
  
    $id          = $request->id;
    $role        = $request->role;
    $permissions = $request->permissions;

    $validatedData = $request->validate([
        'role'    => 'required|min:2|max:50|unique:roles,name,'.$id.',id,deleted_at,NULL',
    ]);
       
      $role       = Role::find($id);
      $role->name = $request->role;
      $role->save();

      if(!empty($permissions)){
        $role->permissions()->sync($permissions);
      }

       if($role->id){
          return $this->returnResponse(true , 'Successfully updated role' , 'index');
        }
        else{
          return $this->returnResponse(false , 'Failed to update role' , 'index');
        }
  }

    public function returnResponse($status , $message , $redirect ){
       
       return redirect()->route('role/'.$redirect)->with('msg' , $message)->with('status' , $status );

    }

    public function status(Request $request){
        
        $this->authorize('status', Role::class);
        
        $id = $request->id;
        $role = Role::find($id);
        $status = $role->status ? '0' : '1';
        $role->status = $status;
        $text =  $status ? 'Active' : 'Inactive';
        if($role->save()){
          return ['status' => true , 'message' => 'Successfully '.$text.' role'];
        }
        else{
          return ['status' => false , 'message' => 'Failed '.$text.' role'];
        }
 
    }

}
