<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Web\TableController;
use App\Notifications\FormPublish;
use App\Mail\FormScheduleMail;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use DataTables;
use App\User;
use App\Role;
use App\Form;
use App\FieldTypes;
use App\FormField;
use App\TabularFormSetting;
use DB;
use App\FormTable;
use Mail;
use App\DataTables\FormGroupDataTable;
use App\DataTables\FormsSubmissionsDataTable;
use App\UserSchedule;
use App\FormGroup;
use App\FormSchedule;
use App\Mail\NotifyMail;

class FormController extends Controller
{

    function index($group_id='',Request $request)
    {   

      if ( base64_encode(base64_decode($group_id, true)) === $group_id){
          $form_id = 0;
      } else {
          $form_id = $group_id;
      }
      if ($form_id) 
      {
        $getGroupId = DB::table('forms_by_groups')->select('group_id')->where('form_id',$form_id)->first();
        $group_id  = base64_encode($getGroupId->group_id);
      }
      $user=Auth::user();
      $group_id=base64_decode($group_id);
      $form_group=FormGroup::find($group_id);
      $group_name=!empty($form_group)?$form_group->group_name:'';
      
      $roles=Role::all();
      $users=User::all();
      $type=base64_decode($request->type);
      if($type=="submissions")
      {
                $page_title = "Forms Submissions List";
      }  else   $page_title = "Forms List";

    if($user->role==1)
        return view('form/index',compact('roles','users' ,'group_name','group_id','page_title'));
      else   return view('form/user_index',compact('roles','users' ,'group_name','group_id','page_title'));
    }
    function ajax_Form(Request $request)

    {
      $userID   = auth::id();
      $group_id = $request->group_id;
      $type     = base64_decode($request->type);

     if(Auth::user()->role == 1){

        if($type == 'submissions'){
          $forms   = Form::select('forms.*' , 'form_groups.group_name')
                          ->join('forms_by_groups' , 'forms_by_groups.form_id' , '=' , 'forms.id')
                          ->join('form_groups' , 'form_groups.id' , '=' , 'forms_by_groups.group_id')
                          ->where(function($query) use($group_id){
                              if(!empty($group_id)){
                                $query->where('form_groups.id' , $group_id);
                              }
                            })
                         ->distinct('forms.form_id')
                         ->withTrashed()
                         ->get();
        }else{
          $forms   = Form::select('forms.*' , 'form_groups.group_name')
                          ->join('forms_by_groups' , 'forms_by_groups.form_id' , '=' , 'forms.id')
                          ->join('form_groups' , 'form_groups.id' , '=' , 'forms_by_groups.group_id')
                          ->where(function($query) use($group_id){
                              if(!empty($group_id)){
                                $query->where('form_groups.id' , $group_id);
                              }
                            })
                         ->distinct('forms.form_id')
                         ->get();
        }

      }else{

          $forms = UserSchedule::select('form_scheduled_users.schedule_id as schedule_id','forms.id as form_id' , 'forms.name' , 'forms.status'  , 'forms.form_type' , 'form_scheduled_forms.user_readed' , 'form_groups.group_name' , 'submit.submit_status' , 'submit.user_submission_request' , 'submit.user_request_status' , 'submit.record_accept_status' , 'submit.admin_resubmission_request' , 'submit.user_resubmission_reason' , 'submit.admin_resubmission_reason' , 'form_schedules.schedule_name' , 'form_schedules.start_date' , 'form_schedules.end_date','form_schedules.created_at')
                                  ->join('form_scheduled_forms' , 'form_scheduled_users.schedule_id' , '=' , 'form_scheduled_forms.schedule_id')
                                  ->join('forms' , 'form_scheduled_forms.form_id' , '=' , 'forms.id')
                                  ->join('forms_by_groups' , 'forms.id' , '=' , 'forms_by_groups.form_id')
                                  ->join('form_groups' , 'forms_by_groups.group_id' , '=' , 'form_groups.id')
                                  ->join('form_schedules' , 'form_scheduled_users.schedule_id' , '=' , 'form_schedules.id')
                                  ->leftJoin('user_form_submitted AS submit', function($join){
                                        $join->on('forms.id', '=', 'submit.form_id')
                                             ->on('form_scheduled_users.user_id', '=', 'submit.user_id')
                                             ->on('form_scheduled_users.schedule_id', '=', 'submit.schedule_id');
                                    })
                                  ->where('form_scheduled_users.user_id' , $userID)
                                  ->whereDate('form_schedules.start_date', '<=' , date('Y-m-d'))
                                  ->where(function($query) use ($group_id){
                                      if (!empty($group_id) && !is_null($group_id)) {
                                        $query->where('form_groups.id' , $group_id);
                                      }
                                    })
                                  ->whereNull('form_schedules.deleted_at')
                                //  ->groupBy('submit.user_id','submit.schedule_id','submit.form_id')
                                  ->distinct('submit.user_id','submit.schedule_id','submit.form_id')
                                  ->orderBy('form_schedules.created_at')
                                  ->get();
      }
      
     $user_id=$userID;
    foreach($forms as $key => $value){
          $form_id=$value->id;
          $total=0;
          $is_submitted=0;
          if($value->form_type=="Tabular")
          {
           $tables=FormTable::where('form_id',$form_id)
                              ->take(1)
                              ->get();
          if($tables)
            {
             foreach($tables as $key1=>$table){
              $table_name=str_replace(' ', '_', strtolower($value->name))."_".$form_id."_".$table->id; 
            if(Schema::hasTable($table_name)){
                    $check_submissions=DB::table($table_name)
                          ->select('user_id')
                          ->distinct('user_id')
                          ->pluck('user_id');
                    if($check_submissions) $total=$total+count($check_submissions);
                    $record_submit= DB::table($table_name)
                                    ->where('user_id',$user_id)
                                    ->count();
                     if($record_submit>0) $is_submitted=1;
                             
                    
                     }
                }
           } 
          }
         else{
          $table=$this->getTableName($value->id);
          $table = $table.'_'.$value->id;
          if(Schema::hasTable($table)){
           $total=DB::table($table)->count();
           $forms[$key]['is_table']     = true;
          }else{
           $forms[$key]['is_table']     = false;
           $total=0;
          }
        }
   $forms[$key]['submissions']=$total;
   $forms[$key]['is_submitted']=$is_submitted;
    }
  if($forms->count()<=0)$forms=[];
   return DataTables::of($forms)->make(true);
  }
  
  function createForm(Request $request)
    {  
        
        $roles=Role::all();
        $users=User::all();
        $form_groups = DB::table('form_groups')->select('id' , 'group_name')->where('status',1)->whereNull('deleted_at')->get();

        return view("form.add",compact('roles','users' , 'form_groups'));
    }

    function delete($id)
    {
     $res=Form::find($id)->delete(); 
      if($res)
       {
            
           $response=[
                      'status'=>1,
                      'message'=>'success'
                     ];
        }
        else{
            $response=['status'=>0,
                   'message'=>'failed'
                   ];
                 
            }
     return response()->json($response);
    }
function store(Request $request)
{
  $input=$request->only('name', 'form_type' , 'group');
  
  $validator=Validator::make($input,[
            'name'        => 'required|max:50|unique:forms,name,NULL,id,deleted_at,NULL',
            "form_type"   => 'required',
            'group'       => 'required'
          ],[
             'name.max' => 'Form name is too long'
          ]);

    if($validator->fails())
    {
      return back()->withErrors($validator)
                    ->withInput();   
    }

      DB::beginTransaction();

        $form=new Form;
        $form->name=$input['name'];
        $form->form_type=$input['form_type'];

       if($form->save()){

         $insertStatus = DB::table('forms_by_groups')->insert(['form_id' => $form->id , 'group_id' => $input['group']]);

         if($insertStatus){
             DB::commit();
            return redirect()->route('form/edit',['form_id'=>base64_encode($form->id) , 'fieldTypeTab' => true]);
          }
              DB::rollback();
        }
          return back()->with(["msg"=>"Failed to add form.","color"=>"danger"]);
}

function edit($id=''){
   if(!empty($id))
   { 

     $form_groups      = DB::table('form_groups')->select('id' , 'group_name')->whereNull('deleted_at')->get();

     $form_id = $id;
     $id=base64_decode($id);
     $view="";

     $form = Form::where('id' , $id)->join('forms_by_groups' , 'forms_by_groups.form_id' , '=' , 'forms.id')->first();

     $field_types=FieldTypes::whereIn('id' , [1,2,3,7,8,4,10,14,18,19])->orderBy('id','asc')->get();
     $form_fields=FormField::with('field_type')->where('form_id',$id)->get();
     $tables_setting=FormTable::where('form_id',$id)->whereNull('deleted_at')->get();
     $roles=Role::all();
     $users=User::all();

    foreach ($field_types as $key1 => $value1) {
            
            foreach ($form_fields as $key2 => $value2) {
                    if($value1->id == $value2->field_type_id){
                       $form_fields[$key2]->field_type_text = $value1->field_type_identifier;
                    }
            }

    }

    $form_name = $this->getTableName($id);

    if($form->form_type=="Tabular")
        $view="form.addTabularForm";
      else
        $view = "form.verticalForm";
        
   return view($view,compact('form','field_types','form_fields','tables_setting' ,'form_name','users','roles','form_id' , 'form_groups'));

   }else return back()->with(["msg"=>"Invalid form id.","color"=>"danger"]);
 }

// function update(Request $request){
  
//   $input = $request->only('name','form_id','group');

//   $validator=Validator::make($input,[
//             'group'       => 'required',
//             'name'        => 'required|unique:forms,name,'.$input['form_id'],
//           ]);

//   if($validator->fails()){
//       return back()->withErrors($validator)
//                     ->withInput();   
//   }
          
//     DB::beginTransaction();

//   $id =  base64_decode($input['form_id']);

//   $old_table = $this->getTableName($id);

//   $form=Form::find($id);

//   if($form){

//     $form->name         = trim($input['name']);

//     if($form->save()){
//       try {

//         if($form->form_type == 'Vertical'){
//           $old_table =  $old_table.'_'.$id;
//           $new_table =  str_replace(' ', '_', strtolower($form->name));
//           $new_table =  $new_table.'_'.$id;

//            if(Schema::hasTable($old_table)){
//               TableController::renameTable($old_table,$new_table);
//            }
//         }

//           $deleteStatus = DB::table('forms_by_groups')->where('form_id' , $id)->delete();
//           $arr = [
//            'form_id'  => (Int) $form->id,
//            'group_id' => (Int) $input['group']
//           ];

//           $insertStatus = DB::table('forms_by_groups')->insert($arr);

//       } catch (Exception $e) {
//            DB::rollback();
//            return back()->with(["msg"=>"Some thing went wrong !!","status"=> false ,"color"=>"danger"]);  
//       }

//       if($insertStatus && $deleteStatus){
//             DB::commit();
//         return redirect()->back()->with(["msg"=>"Form updated successfully.","status"=> true ,"color"=>"success"]);
//       }
//             DB::rollback();
//     }

    
//     return back()->with(["msg"=>"Failed to add form.","status"=> false ,"color"=>"danger"]);

//   }
  
//   return back()->with(["msg"=>"Some thing went wrong !","status"=> false ,"color"=>"danger"]);  

// }

 function update(Request $request){

 $input = $request->only('name','form_id','group');
 $id =  base64_decode($input['form_id']);
 $validator=Validator::make($input,[
           'group'       => 'required',
           'name'        => 'required|max:50|unique:forms,name,'.$id.',id,deleted_at,NULL',
         ],[
             'name.max' => 'Form name is too long'
          ]);

 if($validator->fails()){
   return back()->withErrors($validator)->withInput();   
 }
         
 DB::beginTransaction();

 $form=Form::find($id);
 if($form){
   
   try{  
     //updating form name
     if($form->name != trim($input['name'])){
       
       //Tabular Form
       if($form->form_type == 'Tabular'){
         $tabledata = DB::table('tables')->where('form_id',$id)->get();
         if($tabledata){
           foreach($tabledata as $table){
             $oldFormName = str_replace(' ', '_', strtolower($form->name)).'_'.$id.'_'.$table->id;
             if(Schema::hasTable($oldFormName)){
               $newFormName = str_replace(' ', '_', strtolower(trim($input['name']))).'_'.$id.'_'.$table->id;
               if(!(Schema::hasTable($newFormName))){
                 //rename table name
                 Schema::rename($oldFormName, $newFormName);
               }      
             }            
           }
         }

         //update form name
         $form->name = trim($input['name']);
         if($form->save()){
           $deleteStatus = DB::table('forms_by_groups')->where('form_id' , $id)->delete();
           $arr = [
             'form_id'  => (Int) $form->id,
             'group_id' => (Int) $input['group']
           ];
           $insertStatus = DB::table('forms_by_groups')->insert($arr);
         }  
       }else{
         
         //Vertical Form
         $oldFormName = str_replace(' ', '_', strtolower($form->name)).'_'.$id;
         if(Schema::hasTable($oldFormName)){ 
           $newFormName = str_replace(' ', '_', strtolower(trim($input['name']))).'_'.$id;
           if(!(Schema::hasTable($newFormName))){
             //rename table name
             Schema::rename($oldFormName, $newFormName);

             //update form name
             $form->name = trim($input['name']);
             if($form->save()){
               $deleteStatus = DB::table('forms_by_groups')->where('form_id' , $id)->delete();
               $arr = [
                 'form_id'  => (Int) $form->id,
                 'group_id' => (Int) $input['group']
               ];
               $insertStatus = DB::table('forms_by_groups')->insert($arr);
             }
           }
         } 
       }

     }else{
        //updating form group only
        $deleteStatus = DB::table('forms_by_groups')->where('form_id' , $id)->delete();
        $arr = [
               'form_id'  => (Int) $form->id,
               'group_id' => (Int) $input['group']
        ];
        $insertStatus = DB::table('forms_by_groups')->insert($arr);
    }
   }catch(Exception $e){
     DB::rollback();
     return back()->with(["msg"=>"Some thing went wrong !!","status"=> false ,"color"=>"danger"]);
   }
   
   if($insertStatus && $deleteStatus){
     DB::commit();
     return redirect()->back()->with(["msg"=>"Form updated successfully.","status"=> true ,"color"=>"success"]);
   }
   DB::rollback();
   
   return back()->with(["msg"=>"Failed to add form.","status"=> false ,"color"=>"danger"]);

 }
 
 return back()->with(["msg"=>"Some thing went wrong !","status"=> false ,"color"=>"danger"]);  

}


function updateformField(Request $request)
    {
        $input=$request->only('form_id','table_id','field_setting','form_type','no_of_rows','table_id');
        $res=Form::find($input['form_id']); 
      if($res)
        {
         if(isset($input['table_id']) && !empty($input['table_id']))
         {
          $form_exist=FormField::where('form_id',trim($input['form_id']))
                                ->where('table_id',$input['table_id']);
          if($form_exist) $form_exist->delete(); 
          
         }else{
            $form_exist=FormField::where('form_id',trim($input['form_id']));
            if($form_exist) $form_exist->delete();
         }
          $field_setting=json_decode($input['field_setting']);
          $insert_field=[];
          if(count($field_setting)>0)
          {

            foreach ($field_setting as $key =>$value){
               
                if(empty($value->rules)){

                   $rules = '{"input_type":"false","required":false,"unique":false,"minimum":false,"maximum":false}';

                }else{
                   $rules = $value->rules;
                }
                   
                $insert_field[$key]=[
                                "form_id"=>$input['form_id'],
                                "field_type_id" => $value->field_type=="N/A"?0:$value->field_type,
                                "field_title"   => $value->display_text,
                                "col_name"      => $value->db_colum,
                                "list_order"    => $value->order,
                                "field_name"    => $value->form_field,
                                "rules"         => $rules,
                                "field_type_id"=>$value->field_type=="N/A"?0:$value->field_type,
                                "field_title"=>$value->display_text,
                                "col_name"=>$value->db_colum,
                                "list_order"=>$value->order,
                                "field_name"=>$value->form_field,
                                "table_id"=>isset($input['table_id'])?$input['table_id']:0
                              ];
              }
         
          }
     
        try{
           
            $res=FormField::insert($insert_field);
            }catch(Exception $e)
            {
                 $response=[
                            'status'=>0,
                            'message'=>'Failed to update field setting due to '.$e.getMessage()
                          ];
            }
         if($res)
         {
            $response=[
                      'status'=>1,
                      'message'=>'Form field updated sucessfully'
                    ];
        }else  $response=[
                          'status'=>0,
                          'message'=>'Failed to update field setting'
                         ];
        }
        else{
            $response=[
                        'status'=>0,
                        'message'=>'Failed to update field setting'
                     ];
                 
            }

       return response()->json($response);
    }

 function submit(Request $request)
 {

     if(!empty($request->form_id) && !empty($request->schedule_id))
    {   

    $form_id     = base64_decode($request->form_id);
    $schedule_id = base64_decode($request->schedule_id);
    $user_id     = auth::id();

    $id          = $form_id;

    //   $data['form']         = Form::find($id);
       $data['form']         = DB::table('forms')->where('id',$id)->first();

    $data['form_id']      = $id;
    $data['field_types']  = FieldTypes::whereIn('id' , [1,2,3,7,8,4,10,14,18,19])->orderBy('id','asc')->get();
    $data['form_fields']  = FormField::where('form_id',$id)
                               ->WhereNotIn('field_title',['ID','Created Date','Last Modified'])
                               ->get();

      foreach ($data['field_types'] as $key1 => $value1) {
             
            foreach ($data['form_fields'] as $key2 => $value2) {
                    if($value1->id == $value2->field_type_id){
                       $data['form_fields'][$key2]->field_type = $value1->field_type_identifier;
                    }
                    if(!empty($value2->rules)){
                        $r = unserialize($value2->rules);
                        $data['form_fields'][$key2]->rule = $r;
                    }
            }
    }

    $comments = array();
    //get comments
    if($schedule_id != 0)
      $comments = DB::table('comments')->select('comments.*','users.first_name')
                      ->join('users', 'comments.comment_by', '=', 'users.id')
                      ->where([['schedule_id',$schedule_id]])
                      ->where([['form_id',$form_id]])
                      ->where(function($query) use ($user_id)  {
                          $query->where('comment_by' , $user_id);
                          $query->orWhere('comment_to' , $user_id);
                      })
                      ->orderBy('created_at','ASC')
                      ->get();
                  
    $data['comments']        = $comments;
    
    $data['form_name']       = $this->getTableName($id);
    $data['tabular_setting'] = TabularFormSetting::where('form_id',$id)->first();
    $data['tabular_setting'] = !empty($tabular_setting)?$tabular_setting:[];
    $data['tables_setting']  = FormTable::where('form_id',$id)->whereNull('deleted_at')->get();
    $data['button_title']    = "Submit";
    $data['schedule_id']     = $schedule_id;
    $data['form_id']         = $id;
    $data['table_data']      = [];

      if($data['tables_setting']){
        foreach($data['tables_setting'] as $key=>$value){
              $table_id=$value->id;  
              $table_name=str_replace(' ', '_', strtolower($data['form']->name))."_".$id."_".$table_id;
          if(Schema::hasTable($table_name)){
             $record=DB::table($table_name)
                         ->where('user_id',$user_id)
                         ->where('status','!=',4)
                         ->where('schedule_id',$schedule_id)
                         ->get();
             $data['tables_setting'][$key]['table_data']=$record;
          }
        } 
        $form_name=isset($data['form->name'])?$data['$form->name']:'';  
      } 

    if(!empty($schedule_id)){
       $this->readFormNotifications($id,$schedule_id);
    }

    if($data['form']->form_type=="Tabular")
        $view="form.viewTabularForm";
    else{
         return redirect('create-data?form_id='.base64_encode($form_id).'&schedule_id='.base64_encode($schedule_id));
      //  $view="formdata.add";
    } 
  if(Auth::user()->role==1) $data['view_template']=true;

    return view($view,compact('data'));
    
    }else  return view('form/view')->with(["msg"=>"Invalid form id.","color"=>"danger"]);
 }

  function template(Request $request)
 {

   $id = $request->form_id;

   if(!empty($id))
    {   
    $id=base64_decode($id);
    $data['form']         = Form::find($id);
    $data['form_id']      = $id;
    $data['field_types']  = FieldTypes::whereIn('id' , [1,2,3,7,8,4,10,14,18,19])->orderBy('id','asc')->get();
    $data['form_fields']  = FormField::where('form_id',$id)
                               ->WhereNotIn('field_title',['ID','Created Date','Last Modified'])
                               ->get();

      foreach ($data['field_types'] as $key1 => $value1) {
             
            foreach ($data['form_fields'] as $key2 => $value2) {
                    if($value1->id == $value2->field_type_id){
                       $data['form_fields'][$key2]->field_type = $value1->field_type_identifier;
                    }
                    if(!empty($value2->rules)){
                        $r = unserialize($value2->rules);
                        $data['form_fields'][$key2]->rule = $r;
                    }
            }
    }
    $comments = array();
    //get comments
    if(isset($schedule_id) && $schedule_id != 0)
      $comments = DB::table('comments')->select('comments.*','users.first_name')
                      ->join('users', 'comments.comment_by', '=', 'users.id')
                      ->where([['schedule_id',$schedule_id]])
                      ->where([['form_id',$id]])
                      ->where(function($query) use ($user_id)  {
                          $query->where('comment_by' , $user_id);
                          $query->orWhere('comment_to' , $user_id);
                      })
                      ->orderBy('created_at','ASC')
                      ->get();
                    
    $data['comments']        = $comments;  
    $data['form_name']      = $this->getTableName($id);
    $data['tabular_setting']=TabularFormSetting::where('form_id',$id)->first();
    $data['tabular_setting']=!empty($tabular_setting)?$tabular_setting:[];
    $data['tables_setting']=FormTable::where('form_id',$id)->whereNull('deleted_at')->get();
    $data['button_title']="Submit";

    if($data['form']->form_type=="Tabular")
        $view="form.viewTabularForm";
    else 
        $view="formdata.add";

  if(Auth::user()->role==1) $data['view_template']=true;
    return view($view,compact('data'));
    
    }else  return view('form/view')->with(["msg"=>"Invalid form id.","color"=>"danger"]);

 }

 function updateTabularSetting(Request $request)
 {
       $input=$request->only('form_id','table_id','table_titles','row_data','form_type','label_heading');
       $response=[];
      if(isset($input['table_id']) &&!empty($input['table_id']))
       {
       $table_exist=FormTable::find($input['table_id']);
       if($table_exist)
       $table_obj=$table_exist;
      }else $table_obj=new FormTable;
           $table_obj->row_data=json_encode($input['row_data']);
           $table_obj->table_titles=json_encode($input['table_titles']);
           $table_obj->form_id=$input['form_id'];
           $table_obj->label_heading=isset($input['label_heading'])?$input['label_heading']:"";
         if($table_obj->save()){
                 $response=[
                       'status'=>1,
                       'message'=>'Table setting updated successfully.'
                       ];  
              }
        
        else{
             $response=[
                       'status'=>0,
                       'message'=>'Failed to update row setting'
                       ];
             }

       return response()->json($response);
 }
function deleteTable($form_id='',$table_id=''){
   
  $form_exist  = FormField::where('form_id',trim($form_id))
                           ->where('table_id',trim($table_id));
  
  $table_exist = FormTable::find($table_id);
  
  if($form_exist && $table_exist){

    $table_name = $this->getTableName($form_id);

    $table_name = trim($table_name).'_'.$form_id.'_'.$table_id;

    $flag = false;

    if(Schema::hasTable($table_name)){

       $userData = DB::table($table_name)->count();

       if($userData > 0){
         $form_exist->deleted_at  = date('Y-m-d H:i:s');
         $table_exist->deleted_at = date('Y-m-d H:i:s');
         $flag = true;
       }
    }

      if($flag){
        
      $statusFieldUpdate =  DB::table('form_fields')
            ->where('form_id',trim($form_id))
            ->where('table_id',trim($table_id))
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);

      $statusTableUpdate =  DB::table('tables')
            ->where('id',$table_id)
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);      
      //if($form_exist->update() && $table_exist->update()){
        if($statusFieldUpdate && $statusTableUpdate){
          $response=[
            'status'=>1,
            'message'=>'Table setting deleted successfully.'
          ]; 
        }else{
          $response=[
          'status'=>0,
          'message'=>'Failed to delete table setting flag.'
        ];        
        }
      }else{
        if($form_exist->delete() && $table_exist->delete()){
            Schema::dropIfExists($table_name);
            $response=[
              'status'=>1,
              'message'=>'Table setting deleted successfully.'
            ]; 
        }else{
            $response=[
              'status'=>0,
              'message'=>'Failed to delete table setting.'
            ];        
        }
      }

  }else {
    $response=[
      'status'=>0,
      'message'=>'Failed to delete table setting.'
    ]; 
  }
  return response()->json($response);
} 

  function storeTabularFormField(Request $request){
    $form_id     =  $request->form_id;
    $field_type  =  $request->field_type;
    $field_lable =  $request->field_lable;
    $rules       =  $request->except(['id','field_type','field_lable']);
    $is_option   =  $request->option;

    $form_id     = base64_decode($form_id);
    $table_id=isset($request->table_id)?$request->table_id:0;
    $fieldType   = FieldTypes::find($field_type);

    if(!empty($is_option)){
      $is_option = implode(',',$is_option);
    }

    $form  = Form::find($form_id);

    if(!empty($rules)){
      $rules =  serialize($rules);
    }

    $form_name  = $form->name;

    $field_type_identifier = $fieldType->field_type_identifier;

    $Type  =  $this->fieldType($field_type_identifier);
    if($table_id){
      $table=FormTable::find($table_id);
    }else{ 
      $table = new FormTable();
      $table->form_id = $form_id;
      $table->save();
    }
    
    $col_name=str_replace(' ', '_', strtolower($field_lable));
    $where = [
      ['form_id',$form_id],
      ['table_id',$table->id],
      ['col_name',$col_name]
    ];
//    $chk_field=FormField::where($where)
//                          ->count();
//   if($chk_field) return array('status'=>false , 'message' => 'This colum is already exist.');
       
   //$formField->col_name= 
    
    // $formField = new FormField;
     
    // $formField->form_id       = $form_id;
    // $formField->field_name    = str_replace(' ', '_', strtolower($field_lable));
    // $formField->field_type_id = $field_type;
    // $formField->field_title   = $field_lable;
    // $formField->col_name      = str_replace(' ', '_', strtolower($field_lable));
    
    // if(!empty($is_option)){
    //    $formField->field_options    =  $is_option;
    // }

    // if($table)
    //   $formField->table_id    = $table->id;
    //   $formField->rules         = $rules;

    if($table){
    
      $table_name = str_replace(' ', '_',trim(strtolower($form_name))).'_'.$form_id."_".$table->id;
      
      $fields=[
        [
          'name' =>  str_replace(' ', '_', strtolower($field_lable)),
          'type' => $Type
        ],
      ];

      if(Schema::hasTable($table_name)){
        
        if(Schema::hasColumn($table_name,str_replace(' ', '_', strtolower($field_lable))))
        {
          return array('status'=>false , 'message' => 'This column is already exist.');
        }
      
        $formField = new FormField;
     
        $formField->form_id       = $form_id;
        $formField->field_name    = str_replace(' ', '_', strtolower($field_lable));
        $formField->field_type_id = $field_type;
        $formField->field_title   = $field_lable;
        $formField->col_name      = str_replace(' ', '_', strtolower($field_lable));
      
        if(!empty($is_option)){
          $formField->field_options    =  $is_option;
        }

        if($table)
          $formField->table_id    = $table->id;
      
        $formField->rules         = $rules;
        $formField->save();  

        $error=TableController::updateTable($table_name,$fields); 
      // if($error === false){
      //   return array('status'=>false , 'message' => 'This column is already exist.');die;
      // } 
    
    }else{

      $formField = new FormField;
     
      $formField->form_id       = $form_id;
      $formField->field_name    = str_replace(' ', '_', strtolower($field_lable));
      $formField->field_type_id = $field_type;
      $formField->field_title   = $field_lable;
      $formField->col_name      = str_replace(' ', '_', strtolower($field_lable));
      
      if(!empty($is_option)){
        $formField->field_options    =  $is_option;
      }

      if($table)
        $formField->table_id    = $table->id;
      
      $formField->rules         = $rules;
      $formField->save();


      $comment = "0:Pending1:Submitted2:Accepted 3:Rejected 4:Resubmission";
      $row_label = ['name' =>"row_label",'type'=>"string"];
      $user_id = ['name' =>"user_id",'type' =>"integer","default"=>0];
      
      $status = ['name' =>"status",'type' =>"tinyInteger","default"=>0,'comment'=>$comment];
      
      $schedule_id = ['name' =>"schedule_id",'type' =>"integer","default"=>0];
      array_push($fields,$row_label,$user_id,$status,$schedule_id);
      
      $error=TableController::createTable($table_name,$fields);
    }
 
    $data = array(
      'id'       => $formField->id,
      'type'     => $fieldType->name,
      'lable'    => $field_lable,
      'table_id' =>$table->id,
      'action'   =>"add"
    );

    return array('status'=>true , 'message' => 'successfully added' , 'data' => $data ,'error' => $error);
    
    }else{
      return array('status'=>false , 'message' => 'Failed to add field');
    }
}

public function getValidationRules(Request $request){
      
      $id = $request->id;

      $rules = DB::table('field_validation')
                   ->select('field_validation_rules.id' , 'rule_lable' , 'rule_identifier')
                   ->join('field_validation_rules' , 'field_validation_rules.id' , '=' , 'field_validation.rule_id')
                   ->orderBy('rule_lable','ASC')
                   ->where('form_field_id' , $id)
                   ->get();
       
      if(!empty($rules->toarray())){
        $arr = array(
               'status'  => true,
               'message' => 'rules find',
               'data'    => $rules
        );
      }else{
       $arr = array(
                 'status'  => false,
                 'message' => 'rules not available',
        );
      }

    return $arr;
 }
function storeVerticalFormField(Request $request){
   
    $form_id     =  $request->form_id;
    $field_type  =  $request->field_type;
    $field_lable =  $request->field_lable;
    $rules       =  $request->except(['id','field_type','field_lable' , 'form_id' , 'option', 'field_id']);
    $is_option   =  $request->option;

    $form_id     = base64_decode($form_id);


    $table_name  = $this->getTableName($form_id);
    $table_name  = $table_name.'_'.$form_id;

    if (Schema::hasColumn($table_name, str_replace(' ', '_', strtolower($field_lable))))
    {
        return [
           'status'  => false,
           'type'    => 'error',
           'message' => 'This field lable already exist'
        ];
    }

    if(!empty($rules)){
      $rules =  serialize($rules);
    }
    
    if(!empty($is_option)){
         $is_option = implode(',',$is_option);
    }


   $fieldType   = FieldTypes::find($field_type);

   $form        = Form::find($form_id);

    $form_name   = $form->name;

    $field_type_identifier = $fieldType->field_type_identifier;

    $Type  =  $this->fieldType($field_type_identifier);
    
    // $table_name  = str_replace(' ', '_', strtolower(trim($form_name)));
    // $table_name  = $table_name.'_'.$form_id;
 
    $fields = [
       ['name' =>  str_replace(' ', '_', strtolower($field_lable)) , 'type' => $Type],
    ];

    if(Schema::hasTable($table_name)){
       $error  = TableController::updateTable($table_name,$fields);        
    }else{
       $user_id     = ['name' =>"user_id",'type' =>"integer"];
       $status      = ['name' =>"status",'type' =>"tinyInteger","default"=>1];
       $schedule_id = ['name' =>"schedule_id",'type' =>"integer"];
       array_push($fields,$user_id,$status,$schedule_id);
       $error =  TableController::createTable($table_name,$fields);
    }


    $formField = new FormField;
     
    $formField->form_id       = $form_id;
    $formField->field_name    = str_replace(' ', '_', strtolower($field_lable));
    $formField->field_type_id = $field_type;
    $formField->field_title   = $field_lable;
    $formField->col_name      = str_replace(' ', '_', strtolower($field_lable));
    $formField->rules         = $rules;
    
    if(!empty($is_option)){
       $formField->field_options    =  $is_option;
    }

    if($formField->save()){

    $data = array(
         'id'          => $formField->id,
         'type'        => $fieldType->name,
         'lable'       => $field_lable,
      );

      return array('status' =>  true , 'message' => 'successfully added' , 'data' => $data ,'error' => $error);
    }else{
      return array('status' =>  false , 'message' => 'Failed to add field');
    }

}
function updateVerticalFormField(Request $request){
    
    $field_id    =  $request->field_id;

    $field_lable =  $request->field_lable;
    $rules       =  $request->except(['id','field_type','field_lable' , 'form_id' , 'option', 'field_id']);

    $is_option   =  $request->option;
    
    if(!empty($is_option)){
         $is_option = implode(',',$is_option);
    }
    if(!empty($rules)){
      $rules =  serialize($rules);
    }

      $formField = FormField::find($field_id);
      $old_column_name = $formField->col_name;
      $formField->field_name    = str_replace(' ', '_', strtolower($field_lable));
      $formField->col_name      = str_replace(' ', '_', strtolower($field_lable));
      $formField->field_title   = $field_lable;
      $formField->rules         = $rules;

    if(!empty($is_option)){
       $formField->field_options    =  $is_option;
    }
    $Form   = Form::find($formField->form_id);
    $table_name=str_replace(' ', '_', strtolower($Form->name)).'_'.$formField->form_id;
    $new_column_name =  str_replace(' ', '_', strtolower($field_lable));
    if($formField->save()){
      

      if (Schema::hasTable($table_name))
    {
        if($request->field_type == 1 || $request->field_type == 3 || $request->field_type == 7 || $request->field_type == 8 || $request->field_type == 10 || $request->field_type == 18 || $request->field_type == 19)
          $field_type = 'varchar(255) NULL';
        
        if($request->field_type == 2)
          $field_type = 'LONGTEXT NULL';
        
        if($request->field_type == 4)
          $field_type = 'Text NULL';

        if($request->field_type == 14)
          $field_type = 'DATE NULL';
        
        $field_type_id = $request->field_type;
        $success =  DB::select('ALTER TABLE `'.$table_name.'` CHANGE `'.$old_column_name.'` `'.$new_column_name.'` '.$field_type.'');
       
      }
      else
      {
        return array('status' =>  false , 'message' => 'Something went wrong !');
      }

    $data = array(
         'id'          => $formField->id,
         'lable'       => $field_lable,
      );

      return array('status' =>  true , 'message' => 'successfully update field' , 'data' => $data);
    }else{
      return array('status' =>  false , 'message' => 'Failed to add update field');
    }
}

function deleteVerticalFormField(Request $request){
    

    $id =  $request->id;

    $FormField = FormField::find($id);

    // $Form   = Form::find($FormField->form_id);
    // $table = $Form->name;

    $Form        = Form::find($FormField->form_id);

    $table_name  = str_replace(' ', '_', strtolower($Form->name)).'_'.$FormField->form_id;

    $column_name = str_replace(' ', '_', strtolower($FormField->field_name));

    if (Schema::hasColumn($table_name, $column_name))
    {
      TableController::dropColumn($table_name,$column_name);
    }

    if($FormField->delete())  
      return array('status' =>  true , 'message' => 'successfully deleted' , 'id' => $id);
       else
      return array('status' =>  false , 'message' => 'Failed to add field');

}

function editVerticalFormField(Request $request){
    
    $id =  $request->id;

    $FormField   = FormField::find($id);

    if(!empty($FormField)){
      $FormField->field_rules = unserialize($FormField->rules);
    }

    $FormField->field_options = explode(',', $FormField->field_options);

     if(!empty($FormField))
      return array('status' =>  true , 'message' => 'data found' , 'FormField' => $FormField);
       else
      return array('status' =>  false , 'message' => 'Something went wrong');
}

function updateTabularFormField(Request $request){

    $field_id    =  $request->field_id;
    $is_option   =  $request->option;

    $field_lable =  $request->field_lable;
    $rules       =  $request->except(['id','field_type','field_lable']);

    if(!empty($is_option)){
         $is_option = implode(',',$is_option);
    }

    if(!empty($rules)){
      $rules =  serialize($rules);
    }

    $formField = FormField::find($field_id);
    $old_column_name = $formField->field_name;
      $formField->field_name    = str_replace(' ', '_', strtolower($field_lable));
      $formField->col_name      = str_replace(' ', '_', strtolower($field_lable));
      $formField->field_title   = $field_lable;
      $formField->rules         = $rules;

    if(!empty($is_option)){
       $formField->field_options    =  $is_option;
    }
    $Form   = Form::find($formField->form_id);
    $form_id = base64_decode($request->form_id);
    $table_name=str_replace(' ', '_', strtolower($Form->name))."_".$form_id."_".$formField->table_id;
    $new_column_name =  str_replace(' ', '_', strtolower($field_lable));
    if($formField->save()){ 

    if (Schema::hasTable($table_name))
    {
        if($request->field_type == 1 || $request->field_type == 3 || $request->field_type == 7 || $request->field_type == 8 || $request->field_type == 10 || $request->field_type == 18 || $request->field_type == 19)
          $field_type = 'varchar(255) NOT NULL';
        if($request->field_type == 2)
          $field_type = 'LONGTEXT NULL';
        if($request->field_type == 4)
          $field_type = 'Text NULL';
        if($request->field_type == 14)
          $field_type = 'DATE NOT NULL';
        $field_type_id = $request->field_type;
        $success =  DB::select('ALTER TABLE `'.$table_name.'` CHANGE `'.$old_column_name.'` `'.$new_column_name.'` '.$field_type.'');
       
      }
      else
      {
        return array('status' =>  false , 'message' => 'Something went wrong !');
      }

    $data = array(
         'id'          => $formField->id,
         'lable'       => $field_lable,
         'form_id'     => $request->form_id,
         'action'      =>"update"
      );

      return array('status' =>  true , 'message' => 'successfully update field !' , 'data' => $data);
    }else{
      return array('status' =>  false , 'message' => 'Failed to add update field');
    }
}

function deleteTableFormField(Request $request){
     $id=$request->id;
     try{
    $FormField=FormField::find($id);
    $Form=Form::find($FormField->form_id);
    $table_name=str_replace(' ', '_',trim(strtolower($Form->name))).'_'.$Form->id."_".$FormField->table_id;
    
    $column_name = str_replace(' ', '_',trim(strtolower($FormField->field_name)));

   if(Schema::hasColumn($table_name, $column_name))
  {
           TableController::dropColumn($table_name,$column_name);
    }
if($FormField->delete())  
      return array('status' =>  true , 'message' => 'successfully deleted' , 'id' => $id);
       else
      return array('status' =>  false , 'message' => 'Failed to add field');
     }catch(Exception $ex){ return array('status' =>  true , 'message' =>'Error Message!.'.$ex.getMessage());}
}

function editTableFormField(Request $request){
    
    $id =  $request->id;

    $FormField   = FormField::find($id); 
    if(!empty($FormField)){
      $rulesArr = unserialize($FormField['rules']);
      $validationVal = $rulesArr['validation']; 
      $multiselectVal = (isset($rulesArr['multiselect'])) ? $rulesArr['multiselect'] : ''; 
      $minselectvalue = (isset($rulesArr['min'])) ? $rulesArr['min'] : '';
      $maxselectvalue = (isset($rulesArr['max'])) ? $rulesArr['max'] : ''; 
      return array('status' =>  true , 'message' => 'data found' , 'FormField' => $FormField,'validationVal'=>$validationVal,'multiselectVal'=>$multiselectVal,'min'=>$minselectvalue,'max'=>$maxselectvalue);
    }else{
      return array('status' =>  false , 'message' => 'Something went wrong');
    }
}
function saveTableFormData(Request $request)
{
  $encodeFormId     = $request->form_id;
  $encodeScheduleId = $request->schedule_id;
  $record_store_as  = $request->record_store_as;
  $input            = $request->all();

  $validator=Validator::make($input,[
                'form_id'=>'required',
                'table_id'=>'required',
                'schedule_id'=>'required'
                     ]);

    if($validator->fails())
    {
       return back()->withErrors($validator);
    }

      $form_id     = base64_decode($input['form_id']);
      $schedule_id = base64_decode($request->schedule_id);

      $user          = Auth::user();
      $user_id       = $user->id;
      $no_of_rows    = 0;
      $form_fields   = FormField::where([['form_id',$form_id],['table_id',$input['table_id']]])->get(); 
      $table         = FormTable::find($input['table_id']);
    //  $form          = Form::find($form_id);
        $form          = DB::table('forms')->where('id',$form_id)->first();

      if($form && $table)
      {

         $table_name=str_replace(' ', '_', strtolower($form->name))."_".$form_id."_".$table->id;

        // if(Schema::hasTable($table_name)){
            
        //   $record_submit=DB::table($table_name)
        //                   ->where('user_id',$user_id)
        //                   ->where('status','!=',4)
        //                   ->where('schedule_id',$schedule_id)
        //                   ->count();

        //  if($record_submit>0){
        //     return back()->with(["status"=>false,"msg"=>"You have already submitted the form","color"=>"danger"]);
         
        //   }                      
        // }
       }

      if($table){
        $row_data=json_decode($table->row_data);
        $no_of_rows=count($row_data);
        }
    
      $insert_data=[];
      for($i=0;$i<$no_of_rows; $i++)
      {

          if($form_fields)
           {
             $insert_data[$i]['user_id']    = $user_id;
             $insert_data[$i]['row_label']  = array_get($input['row_label'],$i);
             $insert_data[$i]['status']     = 1;
            $insert_data[$i]['schedule_id'] = $schedule_id;

             foreach($form_fields as $key=>$value)
             {
               $field_name= strtolower($value['field_name']);
               if($request->has($field_name)){
                  $field_arr=$input[$field_name];
                  if(is_array($field_arr))
                  {
                    $field_value=array_get($field_arr,$i);
                    if(is_array($field_value)){
                      if(count($field_value) > 1){
                        $insert_data[$i][$field_name]=implode(',',$field_value);    
                      }else{
                        $insert_data[$i][$field_name]=$field_value[0];  
                      }
                    }else{
                      $insert_data[$i][$field_name]=$field_value;  
                    }
                  }
                }
              }
            }
      }
      

      $insert_other_table['form_id']       = $form_id; 
      $insert_other_table['user_id']       = $user_id;
      $insert_other_table['schedule_id']   = $schedule_id;

      if($record_store_as == 1 )
              $insert_other_table['submit_status']  = 1;
       else
              $insert_other_table['submit_status']  = 0;

    // insert record into table
      if($form && count($insert_data)>0)
               $table_name=str_replace(' ', '_',trim(strtolower($form->name))).'_'.$form->id."_".$table->id;

     if(Schema::hasTable($table_name)){
        
          $existData = DB::table($table_name)->where([['schedule_id',$schedule_id],['user_id',$user_id]])->delete();

         if ($existData == True) {
         
           $resubmit=DB::table($table_name)->insert($insert_data);
           $updateStatus =DB::table('user_form_submitted')->where([['schedule_id',$schedule_id],['user_id',$user_id],['form_id',$form_id]])->update(['submit_status' => $insert_other_table['submit_status']  , 'user_submission_request' => 0 , 'record_accept_status' => 0 , 'admin_resubmission_request' => 0]);

           if($insert_other_table['submit_status'] == '0'){
                   $resubmit = false;
           }

         }else{
         
           $res=DB::table($table_name)->insert($insert_data);
           $other_res=DB::table('user_form_submitted')->insert($insert_other_table); 
           $resubmit = false;

         }

        // $res=DB::table($table_name)->insert($insert_data);
        // $other_res=DB::table('user_form_submitted')->insert($insert_other_table);   
        if ($resubmit == True) {
           return redirect('admin/form')->with(["status"=>true,"msg"=>"Table Record has been resubmit successfully!!.","color"=>"success"]);
        }

        if($insert_other_table['submit_status'] == '0'){
          // return redirect('admin/form-submit?form_id='.$encodeFormId.'&schedule_id='.$encodeScheduleId)->with('msg' , 'Successfully save record as draft')->with('status' , true);
          return array('message' => 'Save as a draft successfully !','status' => true);
        }

        if($res){
           return redirect('admin/form')->with(["status"=>true,"msg"=>"Table Record has been saved successfully!!.","color"=>"success"]);
        }else{
           return back()->with(["status"=>false,"msg"=>"Failed to add record!!.","color"=>"danger"]);
        }

      }else{ 
        return back()->with(["status"=>false,"msg"=>"Table not found in database !!.","color"=>"danger"]);
      }
}

function saveMultiTableFormData(Request $request)
{
  
  $input=$request->all();
  $form_id     = base64_decode($request->form_id);
  $schedule_id = base64_decode($request->schedule_id);
  $record_store_as  = $request->record_store_as;

  $validator=Validator::make($input,[
               'form_id'=>'required',
               'table_id'=>'required',
              'schedule_id'=>'required'
                    ]);
  if($validator->fails()){
    $response = [
                "status"=>false,
                "message"=>$validator->messages()->first()
              ];
    return  $response;
  }else{
    $user_id=Auth::id();
    $no_of_rows=0;
    $form_fields=FormField::where([['form_id',$form_id],['table_id',$input['table_id']]])->get(); 
    $table=FormTable::find($input['table_id']);
    $form=Form::find($form_id);
    if($form && $table){

      $table_name=str_replace(' ', '_', strtolower($form->name))."_".$form_id."_".$table->id;
      // if(Schema::hasTable($table_name)){
          
      //                       $record_submit=DB::table($table_name)
      //                                       ->where('user_id',$user_id)
      //                                       ->where('status','!=',4)
      //                                       ->where('schedule_id',$schedule_id)
      //                                       ->count();
      //                        if($record_submit>0){

      //                           $response=["status"=>false,"message"=>"You have already submitted the form"];
      //                           return $response;

      //                         }
                                       
                              
      //              }

    }
    
    if($table){
      $row_data=json_decode($table->row_data);
      $no_of_rows=count($row_data);
    }
    $user=Auth::user();
    $user_id=$user->id;
    $insert_data=[];
    for($i=0;$i<$no_of_rows; $i++)
    {
      if($form_fields){
        $insert_data[$i]['user_id']=$user_id;
        $insert_data[$i]['row_label']=array_get($input['row_label'],$i);
        $insert_data[$i]['status']=1;
        $insert_data[$i]['schedule_id']=$schedule_id;
        foreach($form_fields as $key=>$value)
        {
          $field_name= strtolower($value['field_name']);
          if($request->has($field_name)){
            $field_arr=$input[$field_name];
            if(is_array($field_arr)){
              $field_value=array_get($field_arr,$i);
              if(is_array($field_value)){
                if(count($field_value) > 1){
                  $insert_data[$i][$field_name]=implode(',',$field_value);    
                }else{
                  $insert_data[$i][$field_name]=$field_value[0];  
                }
              }else{
                $insert_data[$i][$field_name]=$field_value;  
              }
              
            }
          }
        }

      } 
    }
    
    if($record_store_as == 1 )
      $insert_other_table['submit_status']  = 1;
    else
      $insert_other_table['submit_status']  = 0;

    $insert_other_table['form_id']       = $form_id; 
    $insert_other_table['user_id']       = $user_id;
    $insert_other_table['schedule_id']   = $schedule_id;
    // $insert_other_table['submit_status'] = 1;

    // insert record into table
    if($form && count($insert_data)>0)
      $table_name=str_replace(' ', '_',trim(strtolower($form->name))).'_'.$form->id."_".$table->id;
      
      if(Schema::hasTable($table_name)){
        $existData = DB::table($table_name)->where([['schedule_id',$schedule_id],['user_id',$user_id]])->delete();
        if ($existData == True) {
          $resubmit=DB::table($table_name)->insert($insert_data);
          $updateStatus =DB::table('user_form_submitted')->where([['schedule_id',$schedule_id],['user_id',$user_id],['form_id',$form_id]])->update(['submit_status' => $insert_other_table['submit_status'] , 'user_submission_request' => 0 , 'record_accept_status' => 0 , 'admin_resubmission_request' => 0]);
        }else{
          $res=DB::table($table_name)->insert($insert_data);
          $other_res=DB::table('user_form_submitted')->insert($insert_other_table);  
           
          $resubmit = false;
        }
        if ($resubmit == True) {
          $response=["status"=>true,"message"=>"Table Record has been resubmited successfully!."];
          return $response;
        }
        if($res){
          $response=["status"=>true,"message"=>"Table Record has been saved successfully!."];
          return $response;
        }else{
          $response=["status"=>false,"message"=>"Failed to add record!!."];
          return $response;
        }
    }else {
      $response=["status"=>false,"message"=>"Table not found!!."];
      return $response;
    }
  }
}

function fieldType($identifier = null){

    if($identifier == 'textarea')
       return 'longText';
    
    if($identifier == 'select')
       return 'text'; 
    
    if($identifier == 'number')
       return 'integer';

    if($identifier == 'float')
       return 'double';

    if($identifier == 'date')
       return 'date';

    if($identifier == 'time')
       return 'time';

    if($identifier == 'time')
       return 'year';

    return 'string';
     
}

  public function getTableName($id = null){

    if($id){
      // $Form      = Form::find($id);
      // $table     = str_replace(' ', '_', strtolower($Form->name));
      $Form      = DB::table('forms')->where('id',$id)->first();
      $table     = str_replace(' ', '_', strtolower($Form->name));
      return $table;
    }
    return false;
  }

    // public function publish(Request $request){

        
    //     $roles=Role::all();
    //     $users=User::all();

    //     return view('form.publish',compact('requets','roles','users'));
    // }

    // public function publishStore(Request $request){

    //        $all_role      = $request->all_role;
    //        $specific_role = $request->specific_role;
    //        $specific_user = $request->specific_user;
    //        $roles         = $request->roles;
    //        $users         = $request->users;
    //        $form_id       = $request->id;

    //        if(!empty($all_role)){

    //           $allusersData = User::select('id','first_name')->get();

    //           if(!empty($allusersData)){
    //             foreach ($allusersData as $user) {
    //                 $user->form_id = $form_id;
    //                 User::find($user->id)->notify(new FormPublish($user));
    //             }
    //          }

    //          return response([
    //              'status'  => true,
    //              'message' => 'successfully publish'
    //           ]);
    //        }

    //        if(empty($roles)){
    //           $roles  = array();
    //        }

    //        if(empty($users)){
    //           $users  = array();
    //        }

    //        $usersIds   = $this->getUsersIdRole($roles);

    //        if(!empty($usersIds)){
    //            foreach ($usersIds as $userId) {
    //                  array_push($users , $userId->id);
    //            }
    //        }
   
    //        $allUsersIds =  array_unique($users);

    //        $userData = $this->getUsersById($allUsersIds);
           
    //        if(!empty($userData)){
    //           foreach ($userData as $user) {
    //               $user->form_id = $form_id;
    //               User::find($user->id)->notify(new FormSchedule($user));
    //           }
    //        }

    //        return response([
    //              'status'  => true,
    //              'message' => 'successfully publish'
    //           ]);
    // }

    // public function assignFormUsers(Request $request){

    //    // $roles = Role::all();
        
    //     $users = DB::table('user_forms')
    //                  ->select('users.id' , 'users.first_name' , 'users.last_name')
    //                  ->join('users' , 'users.id' , '=' , 'user_forms.user_id')
    //                  ->join('forms' , 'forms.id' , '=' , 'user_forms.form_id')
    //                  ->where('user_forms',$form_id)
    //                  ->get();
    //     return $users;

    // }
public function storeChangeRequest(Request $request){
        $input=$request->all();
        $validator=Validator::make($input,[
               'message' =>'required',
               "form_id"=>"required"
             ]);
       if($validator->fails())
       {
         return back()->withErrors($validator)
                       ->withInput();   
       }else{
            $user=Auth::user();
            $user_id=$user->id;
            $form_id=base64_decode($input['form_id']);
            $message=$input['message'];
            $res=DB::table('user_requests')->insert(
                                    [
                                     "form_id"=>$form_id,
                                     "user_id"=>$user_id,
                                     "message"=>$message,
                                     "request_for"=>"change"
                                     ]
                                    );
           if($res) return redirect()->back()->with(["msg"=>"Request has been  sent successfully.","status"=>true]); 
           else     return redirect()->back()->with(["msg"=>"Failed to send change request","status"=>false]); 
          }
}

 public function getUsersIdRole($roles = array()){
      
       if(!empty($roles)){
          $users =  DB::table('users')
                         ->select('id')
                         ->whereIn('role',$roles)
                         ->get();

         return $users->toArray();

       }

    }
public function getUsersById($ids = array()){
       
       if(!empty($ids)){
          $users =  DB::table('users')
                   ->select('id','first_name' , 'email')
                   ->whereIn('id',$ids)
                   ->get();

          return $users->toarray();

       }   
    }
 function showChangeRequest($id="")
 {
   if(!empty($id))
    {
    $user=Auth::user();
    $user_id=$user->id;
    $data=[];
    $id=base64_decode($id);
   
    $data['form']      = Form::find($id);
 
    $data['field_types'] = FieldTypes::all();

    $data['form_fields']  = FormField::where('form_id',$id)
                               ->WhereNotIn('field_title',['ID','Created Date','Last Modified'])
                               ->get();

    foreach ($data['field_types'] as $key1 => $value1) {
            
            foreach ($data['form_fields'] as $key2 => $value2) {
                    if($value1->id == $value2->field_type_id){
                       $data['form_fields'][$key2]->field_type = $value1->field_type_identifier;
                    }
                }
            }

    $comments = array();
    //get comments
    if(isset($schedule_id) && $schedule_id != 0)
      $comments = DB::table('comments')->select('comments.*','users.first_name')
                      ->join('users', 'comments.comment_by', '=', 'users.id')
                      ->where([['schedule_id',$schedule_id]])
                      ->where([['form_id',$id]])
                      ->where(function($query) use ($user_id)  {
                          $query->where('comment_by' , $user_id);
                          $query->orWhere('comment_to' , $user_id);
                      })
                      ->orderBy('created_at','ASC')
                      ->get();
                    
    $data['comments']        = $comments;         
    $data['form_name']      = $this->getTableName($id);
    $data['tabular_setting']=TabularFormSetting::where('form_id',$id)->first();
    $data['tabular_setting']=!empty($tabular_setting)?$tabular_setting:[];
    $data['tables_setting']=FormTable::where('form_id',$id)->get();
    $data['table_data']=[];
    $data['button_title']="Submit Change Request";
    //$tables=$data['tables_setting'];
    if($data['tables_setting'])
        {
            foreach($data['tables_setting'] as $key=>$value)
              {
                  $table_id=$value->id;  
                  $table_name=str_replace(' ', '_', strtolower($data['form']->name))."_".$id."_".$table_id;
              if(Schema::hasTable($table_name)){
                 $record=DB::table($table_name)
                                 ->where('user_id',$user_id)
                                 ->get();
                 $data['tables_setting'][$key]['table_data']=$record;
              }
            } 
            
         $form_name=isset($data['form->name'])?$data['$form->name']:'';  
        }
  return view("form.viewTabularForm",compact('data'));
    }else return back()->with(["msg"=>"Invalid form id.","color"=>"danger"]); 
 } 
 function viewTabularData($id="",Request $request)
 {
    
   if(!empty($id))
    {
      $user              = Auth::user();
      $user_id           = isset($request->user_id)?base64_decode($request->user_id):$user->id;
      $data              = [];
      $id                = base64_decode($id);
      $schedule_id       = !empty($request->schedule_id)?base64_decode($request->schedule_id):0;
  //  $data['form']        = Form::find($id);
    $data['form']        = DB::table('forms')->where('id',$id)->first();
    $data['field_types'] = FieldTypes::all();
    $data['form_fields'] = FormField::where('form_id',$id)
                                     ->WhereNotIn('field_title',['ID','Created Date','Last Modified'])
                                     ->get();

    foreach ($data['field_types'] as $key1 => $value1) {
        foreach ($data['form_fields'] as $key2 => $value2) {
                if($value1->id == $value2->field_type_id){
                   $data['form_fields'][$key2]->field_type = $value1->field_type_identifier;
                }
            }
    }

    $comments = array();
    //get comments
    if($schedule_id != 0)
      $comments = DB::table('comments')->select('comments.*','users.first_name')
                      ->join('users', 'comments.comment_by', '=', 'users.id')
                      ->where([['schedule_id',$schedule_id]])
                      ->where([['form_id',$id]])
                      ->where(function($query) use ($user_id)  {
                          $query->where('comment_by' , $user_id);
                          $query->orWhere('comment_to' , $user_id);
                      })
                      ->orderBy('created_at','ASC')
                      ->get();
                    
    $data['comments']        = $comments;                                 
    $data['form_name']       = $this->getTableName($id);
    $data['tabular_setting'] = TabularFormSetting::where('form_id',$id)->first();
    $data['tabular_setting'] = !empty($tabular_setting)?$tabular_setting:[];
    $data['tables_setting']  = FormTable::where('form_id',$id)->whereNull('deleted_at')->get();
    $data['table_data']      = [];
    $data['view_only']       = true;
    $data['form_id']         = $id;

    if($data['tables_setting']){
      foreach($data['tables_setting'] as $key=>$value){
            $table_id=$value->id;  
            $table_name=str_replace(' ', '_', strtolower($data['form']->name))."_".$id."_".$table_id;
        if(Schema::hasTable($table_name)){
           $record=DB::table($table_name)
                       ->where('user_id',$user_id)
                       ->where('status','!=',4)
                       ->where('schedule_id',$schedule_id)
                       ->get();

           $data['tables_setting'][$key]['table_data']=$record;
        }
      } 
      $form_name=isset($data['form->name'])?$data['$form->name']:'';  
    } 
  
     return view("form.viewTabularForm",compact('data'));
  
    }else return back()->with(["msg"=>"Invalid form id.","color"=>"danger"]); 
 } 
public function submission(FormsSubmissionsDataTable $dataTable,$form_id='',Request $request)
    {
      $data=[];
      if($request->has('id'))
      {
        $group_name= '';
        $formdata = DB::table('forms_by_groups')
                    ->where('form_id',base64_decode($request->id))->first();
        if($formdata){
          $groupdata = DB::table('form_groups')
                        ->select('group_name')
                        ->where('id',$formdata->group_id)->first();
          if($groupdata){
            $data['group_name'] = $groupdata->group_name;
          }
        }

        $form_id=base64_decode($request->id);
        $data['form_id']=$form_id;
        $schedule_id=base64_decode($request->schedule_id); 
        $scheduleData = FormSchedule::select('schedule_name')->where('id',$schedule_id)->first();
      if($scheduleData)
        $data['schedule_name'] = $scheduleData->schedule_name;

      }
    return $dataTable->with(['form_id'=>$form_id,'schedule_id'=>$schedule_id])->render('form/submission_groups',compact('data'));
    } 
function schedule($id="",Request $request)
 {
    $user=Auth::user();
    if($request->ajax()){
      
      $form_id=$request->form_id;
      $user_id=$user->id;
      $schduled_id=DB::table('form_scheduled_users as fu')
                        ->select('fu.form_id,ff.scheduled_id')
                        ->leftJoin('form_scheduled_forms as ff','ff.scheduled_id','=',"fu.scheduled_id")
                       ->where('fu.user_id',$user_id)
                       ->where('ff.form_id',$form_id)
                        ->pluck('fu.scheduled_id');
      $scheduled_data=DB::table('form_schedules')
                    ->whereIn('id',$scheduled_id)
                    ->get();


      if($scheduled_data)               
         return Datatables::of($data)->make();

       }else
       {
           if(!empty($id))
            {   
                $id=base64_decode($id);
                $data['form']=Form::find($id);
               
                return view('schedules/user_schedule',compact('data'));
            
            }else  return view('form/view')->with(["msg"=>"Invalid form id.","color"=>"danger"]);
     }

}

public function getTableColumns($table)
    {
        return DB::getSchemaBuilder()->getColumnListing($table);
    }

    public function readFormNotifications($id = null , $schedule = null){
        if(!is_null($id) && !empty($id) && !is_null($schedule) && !empty($schedule)){
              $userReads = DB::table('form_scheduled_forms')
                             ->join('form_schedules' , 'form_scheduled_forms.schedule_id' , '=' , 'form_schedules.id')
                             ->select('user_readed')
                             ->where('form_id' , $id)
                             ->where('schedule_id',$schedule)
                             ->whereDate('form_schedules.start_date', '<=' , date('Y-m-d'))
                             ->first();
        
                $userReadsArr   = array();
                $newUserReadArr = null;
                if(!empty($userReads) && !is_null($userReads)){
                     $userReadsArr   = explode(',', $userReads->user_readed);
                }

                if(!in_array(auth::id(), $userReadsArr)){
                         array_push($userReadsArr,auth::id());
                         $newUserReadArr = implode(',', $userReadsArr);
                         $userReads = DB::table('form_scheduled_forms')
                                         ->select('user_readed')
                                         ->where('form_id' , $id)
                                         ->where('schedule_id',$schedule)
                                         ->update(['user_readed' => $newUserReadArr]);
                }
        }
    }

    public function userResubmissionRequest(Request $request){

      $form_id     = base64_decode($request->form_id);
      $schedule_id = base64_decode($request->schedule_id);
      $resubmission_reason = $request->resubmission_reason;
      $user_id     = Auth::id();

      $status = DB::table('user_form_submitted')
                  ->where([
                           'form_id'     => $form_id,
                           'schedule_id' => $schedule_id,
                           'user_id'     => $user_id,
                        ])
                  ->update(['user_submission_request' => 1 , 'admin_resubmission_request' => 0 ]);

      if($status){
        
        $admin = 1;
        $admindata = User::select('id')->where('role',1)->first();
        if($admindata)
          $admin = $admindata->id; 
        
        //Insert Data into comments
        DB::table('comments')->insert([
          'schedule_id' => $schedule_id, 
          'form_id' => $form_id, 
          'comment_by' => $user_id,
          'comment_to' => $admin,//admin
          'comment'=>$resubmission_reason
        ]);

          $formData = FormSchedule::select('users.first_name' , 'users.last_name' , 'forms.name as form_name' , 'form_schedules.schedule_name')
                      ->join('form_scheduled_forms' , 'form_schedules.id' , '=' , 'form_scheduled_forms.schedule_id' )
                      ->join('form_scheduled_users' , 'form_schedules.id' , '=' , 'form_scheduled_users.schedule_id' )
                      ->join('users' , 'form_scheduled_users.user_id' , '=' , 'users.id' )
                      ->join('forms' , 'form_scheduled_forms.form_id' , '=' , 'forms.id' )
                      ->where('users.id',$user_id)
                      ->where('forms.id',$form_id)
                      ->where('form_schedules.id',$schedule_id)
                      ->first();
          

          if(!empty($formData) && !is_null($formData)){
            $name = $formData->first_name.' '.$formData->last_name;
            
            $data = array(
                'user_name'     => $name,
                'admin_email'   => auth::user()->email,
                'form_name'     => $formData->form_name,
                'schedule_name' => $formData->schedule_name,
                'template'      => 'user_resubmission_request_mail_to_admin',
                'subject'       => 'Resubmission Request',
            );
            
            
            if(!empty($data) && !empty($data['admin_email'] && !is_null($data['admin_email']))){
                 Mail::to($data['admin_email'])->send(new NotifyMail($data));
            }
          }

          
          return redirect('admin/form')->with('status' , true)->with('msg' , 'Successfully request has been sent');
       }

         return redirect('admin/form')->with('status' , false)->with('msg' , 'Failed to send request');

    }

    public function adminResubmissionRequest(Request $request){
           
      $encodeFormId = $request->form_id;
      $form_id     = base64_decode($request->form_id);
      $schedule_id = base64_decode($request->schedule_id);
      $user_id     = base64_decode($request->user_id);
      $resubmission_reason = $request->resubmission_reason;
         
      $status = DB::table('user_form_submitted')
                  ->where([
                           'form_id'     => $form_id,
                           'schedule_id' => $schedule_id,
                           'user_id'     => $user_id,
                  ])
                  ->update(['admin_resubmission_request' => 1 , 'user_submission_request' => 0 , 'submit_status' => 2, 'record_accept_status' => 0]);
        
       if($status){

        //Insert Data into comments
        DB::table('comments')->insert([
          'schedule_id' => $schedule_id, 
          'form_id' => $form_id, 
          'comment_by' => Auth::user()->id,//admin
          'comment_to' => $user_id,
          'comment'=>$resubmission_reason
        ]);

           $formData = FormSchedule::select('users.first_name' , 'users.last_name' , 'users.email as user_email' , 'forms.name as form_name' , 'form_schedules.schedule_name')
                      ->join('form_scheduled_forms' , 'form_schedules.id' , '=' , 'form_scheduled_forms.schedule_id' )
                      ->join('form_scheduled_users' , 'form_schedules.id' , '=' , 'form_scheduled_users.schedule_id' )
                      ->join('users' , 'form_scheduled_users.user_id' , '=' , 'users.id' )
                      ->join('forms' , 'form_scheduled_forms.form_id' , '=' , 'forms.id' )
                      ->where('users.id',$user_id)
                      ->where('forms.id',$form_id)
                      ->where('form_schedules.id',$schedule_id)
                      ->first();
          

          if(!empty($formData) && !is_null($formData)){
            $name = $formData->first_name.' '.$formData->last_name;
            
            $data = array(
                'user_name'     => $name,
                'user_email'    => $formData->user_email,
                'form_name'     => $formData->form_name,
                'schedule_name' => $formData->schedule_name,
                'template'      => 'admin_resubmission_request_mail_to_user',
                'subject'       => 'Resubmission Request',
            );
            
            if(!empty($data) && !empty($data['user_email'] && !is_null($data['user_email']))){
                 Mail::to($data['user_email'])->send(new NotifyMail($data));
            }
          }

          return redirect('admin/submissions?id='.$encodeFormId.'&schedule_id='.$request->schedule_id)->with('status' , true)->with('msg' , 'Successfully request has been sent');
       }else{
         return redirect('admin/submissions?id='.$encodeFormId.'&schedule_id='.$request->schedule_id)->with('status' , false)->with('msg' , 'Failed to send request');
        }
    }


    public function acceptRecord(Request $request){

        $form_id     = base64_decode($request->form_id);
        $user_id     = base64_decode($request->user_id);
        $schedule_id = base64_decode($request->schedule_id);

        $status = DB::table('user_form_submitted')
             ->where([
               'form_id'     => $form_id,
               'user_id'     => $user_id,
               'schedule_id' => $schedule_id,
              ])
             ->update(['record_accept_status' => 1 , 'submit_status' => 1 , 'user_submission_request' => 0 , 'admin_resubmission_request' => 0 , 'user_resubmission_reason' => '','admin_resubmission_reason' => '' ]);

        if($status){

            $formData = FormSchedule::select('users.first_name' , 'users.last_name' , 'users.email as user_email' , 'forms.name as form_name' , 'form_schedules.schedule_name')
                      ->join('form_scheduled_forms' , 'form_schedules.id' , '=' , 'form_scheduled_forms.schedule_id' )
                      ->join('form_scheduled_users' , 'form_schedules.id' , '=' , 'form_scheduled_users.schedule_id' )
                      ->join('users' , 'form_scheduled_users.user_id' , '=' , 'users.id' )
                      ->join('forms' , 'form_scheduled_forms.form_id' , '=' , 'forms.id' )
                      ->where('users.id',$user_id)
                      ->where('forms.id',$form_id)
                      ->where('form_schedules.id',$schedule_id)
                      ->first();
          

          if(!empty($formData) && !is_null($formData)){
            $name = $formData->first_name.' '.$formData->last_name;
            
            $data = array(
                'user_name'     => $name,
                'user_email'    => $formData->user_email,
                'form_name'     => $formData->form_name,
                'schedule_name' => $formData->schedule_name,
                'template'      => 'record_accepted_mail_to_user',
                'subject'       => 'Record Accepted',
            );
            
            if(!empty($data) && !empty($data['user_email'] && !is_null($data['user_email']))){
                 Mail::to($data['user_email'])->send(new NotifyMail($data));
            }
          }

           return [
            'status'  => true,
            'message' => 'User Record has been accepted',
           ];
        }else{
          return [
            'status'  => false,
            'message' => 'Failed To accept record,Please try letter',
           ];
        }

    }

    public function acceptSubmissionRequest(Request $request){
        $form_id     = base64_decode($request->form_id);
        $user_id     = base64_decode($request->user_id);
        $schedule_id = base64_decode($request->schedule_id);

        $status = DB::table('user_form_submitted')
             ->where([
               'form_id'     => $form_id,
               'user_id'     => $user_id,
               'schedule_id' => $schedule_id,
              ])
             ->update(['user_submission_request' => 2 , 'submit_status' => 2 , 'record_accept_status' => 0 ]);

        if($status){

            $formData = FormSchedule::select('users.first_name' , 'users.last_name' , 'users.email as user_email' , 'forms.name as form_name' , 'form_schedules.schedule_name')
                      ->join('form_scheduled_forms' , 'form_schedules.id' , '=' , 'form_scheduled_forms.schedule_id' )
                      ->join('form_scheduled_users' , 'form_schedules.id' , '=' , 'form_scheduled_users.schedule_id' )
                      ->join('users' , 'form_scheduled_users.user_id' , '=' , 'users.id' )
                      ->join('forms' , 'form_scheduled_forms.form_id' , '=' , 'forms.id' )
                      ->where('users.id',$user_id)
                      ->where('forms.id',$form_id)
                      ->where('form_schedules.id',$schedule_id)
                      ->first();
          
          if(!empty($formData) && !is_null($formData)){
            $name = $formData->first_name.' '.$formData->last_name;
            
            $data = array(
                'user_name'     => $name,
                'user_email'    => $formData->user_email,
                'form_name'     => $formData->form_name,
                'schedule_name' => $formData->schedule_name,
                'template'      => 'user_resubmission_request_accepted_mail_to_user',
                'subject'       => 'Accepted Resubmission Request',
            );
            
            if(!empty($data) && !empty($data['user_email'] && !is_null($data['user_email']))){
                 Mail::to($data['user_email'])->send(new NotifyMail($data));
            }
          }

           return [
            'status'  => true,
            'message' => 'User Request has been accepted',
           ];
        }else{
          return [
            'status'  => false,
            'message' => 'Failed To accept request,Please try letter',
           ];
        }
    }

} 

