<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Validator;
use App\DataTables\FormGroupDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\FormGroup;
use App\Form;
use DB;
use auth;

class FormGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FormGroupDataTable $dataTable,Request $request)
    {  
        $type=base64_decode($request->type);
        if($type=="submissions")
        {
          $data['page_title']     = "Form Submissions Groups";
        }else $data['page_title'] = "Form Groups ";
        $data['forms']=Form::all();
        return $dataTable->with('type',$type)->render('form_groups.index',compact('data'));
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
   //  $form_ids   = $request->forms;
       $group_name = $request->group_name;

     // Setup the validator
     $rules = [
              //  'forms'       => 'required',
               'group_name'=> 'required|unique:form_groups,group_name,NULL,id,deleted_at,NULL',
      ];

     if($id){
         $rules['group_name'] = 'required|unique:form_groups,group_name,'.$id.',id,deleted_at,NULL';
     }

      $validator = Validator::make($request->all(), $rules);

      if($validator->fails()){
            return $this->backResponse('error','failed to add group',$validator->errors());
      }

       $data=[
          'group_name' => $group_name,
          'created_at' =>  \Carbon\Carbon::now(), 
          'updated_at' => \Carbon\Carbon::now(),
          'created_by' => auth::id(),
       ];
    
        if($id){
          $status   = DB::table('form_groups')->where('id',$id)->update($data);

          $status = true;

          // if(!empty($id) && !empty($form_ids)){

          //     DB::table('forms_by_groups')->where('group_id',$id)->delete();

          //      $arr = array();
          //      foreach ($form_ids as $key => $value) {
          //           $arr[$key]['form_id']  = $value;
          //           $arr[$key]['group_id'] = $id;
          //      }

          //     DB::table('forms_by_groups')->insert($arr);

          //     $status = true;

          // }

        }else{
          $insertId = DB::table('form_groups')->insertGetId($data);

          if($insertId)
               $status = true;

          // if(!empty($insertId) && !empty($form_ids)){
          //       $arr = array();
          //      foreach ($form_ids as $key => $value) {
          //           $arr[$key]['form_id']  = $value;
          //           $arr[$key]['group_id'] = $insertId;
          //      }

          //     DB::table('forms_by_groups')->insert($arr);

          //     $status = true;

          // }
       
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

       $group = FormGroup::select('form_groups.id' , 'form_groups.group_name')
                              ->where('form_groups.id',$id)
                              ->first();

       if(!empty($group)){
        $forms = Form::select('forms.name' , 'forms.id')
                        ->where('forms_by_groups.group_id',$group->id)
                        ->join('forms_by_groups' , 'forms_by_groups.form_id' , '=' , 'forms.id')
                        ->get();
       }

       if(!empty($forms)){
           $group->formData = $forms;
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

       $status = FormGroup::where('id',$id)->delete();

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

      public function getAllGroups(Request $request){
        
        

        if(!empty($groups))
         return ['status' => true , 'message' => 'gorup found', 'data' => $groups];
       else
         return ['status' => false , 'message' => 'gorup not available found'];
 
    }

    public function getGroups(Request $request){
        
        $id  = $request->id;
        $ids = $request->ids;
        
        if(!empty($id) && !is_null($id)){
          $groups      =  FormGroup::select('id' , 'group_name')->where('form_id' ,$id)->get();
        }

        if(!empty($ids) && !is_null($ids)){
          $groups      =  FormGroup::select('id' , 'group_name' , 'form_id')->whereIn('form_id' ,$ids)->get();
        }

        if(empty($ids) || is_null($ids)){
           $groups = FormGroup::select('id' , 'group_name')->where('id' , 1 )->get();
        }

        if(!empty($groups))
         return ['status' => true , 'message' => 'gorup found', 'data' => $groups];
       else
         return ['status' => false , 'message' => 'gorup not available found'];
 
    }

    public function status(Request $request){
        
        $id     = $request->id;
        $group   = FormGroup::find($id);
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
