<?php

namespace App\Http\Controllers\Web;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Hash;
use App\Mail\Registration;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Web\SetRules;
use Datatables;
use App\User;
use App\Role;
use App\Form;
use App\FormField;
use App\FieldTypes;
use Excel;
use App\Exports\FormDataExport;
use DB;
use Mail;
use Image;
use Auth;

class FormDataController extends Controller
{
    public function index(Request $request){

        $form_id = $request->id;
        
        if(!empty($form_id)){
           $form_id = base64_decode($form_id);
        }

        $table = $this->getTableName($form_id);

        $data['form_name'] = $table;

        $data['columns']   = $this->getTableColumns($table);

        $data['formData']  = DB::table($table)->get();

        if($request->ajax())
        {
          $data=DB::table($table)->get();
          return datatables()->of($data)->make(true);
        }

        return view('formdata.index',compact('data'));

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $id      = base64_decode($request->form_id);
        $form_id = $id; 
        $schedule_id = base64_decode($request->schedule_id);
        $user_id     = auth::id();

   // $id = $request->id;

    // $schedule = $request->schedule;
    // $id = base64_decode($id);

    // if(!empty($schedule)){
    //     $this->readFormNotifications($id,$schedule);
    // }
    $table = $this->getTableName($form_id);

    $table = $table.'_'.$form_id;

    $data['form']        = Form::find($id);
 
    $data['field_types'] = FieldTypes::all();


    $data['form_fields']  = FormField::where('form_id',$id)
                               ->WhereNotIn('field_title',['ID','Created Date','Last Modified'])
                               ->get();

    $where = array(
           'user_id'     => $user_id,
           'schedule_id' => $schedule_id,
    );

    $data['formData']    = DB::table($table)->where($where)->first();

    if(!empty($data['formData']) && !is_null($data['formData'])){
        foreach ($data['formData'] as $key2 => $value2) {
            foreach ($data['form_fields'] as $key3 => $value3) {
                if(strtolower($value3->field_name) == strtolower($key2)){
                    $data['form_fields'][$key3]->field_value = $value2;   
                }
            }
        }
    }

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

    $data['form_name']      = $this->getTableName($id);

    return view('formdata.add',compact('data'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token' , 'form_id' , 'save_record_as');
        
        $form_id        = base64_decode($request->form_id);
        $schedule_id    = base64_decode($request->schedule_id);
        $user_id        = auth::id();
        $save_record_as = $request->save_record_as;

        if(!empty($save_record_as) && $save_record_as == 1){
            $save_record_as = '1';
        }else{
            $save_record_as = '0';
        }

        $table = $this->getTableName($form_id);

        $table = $table.'_'.$form_id;

        $allRules = array();
        $input_fields = array();
       
        foreach($data as $key => $value) {

            $type = gettype($value);
            if($type == 'array'){
                $data[$key] = implode(',', $value);    
            }
            $input_fields[] = $key;
        } 
        
          if($save_record_as == 1){
             
             $dataID = DB::table($table)->where(['user_id' => $user_id , 'schedule_id' => $schedule_id])->select('id')->first();

             if(!empty($dataID) && !is_null($dataID) && !empty($dataID->id) && !is_null($dataID))
                  $validate = SetRules::setRules($form_id,$input_fields,$dataID->id); // input_fields in array formate
              else
                  $validate = SetRules::setRules($form_id,$input_fields); // input_fields in array formate

            $request->validate($validate);

          }

        $destinationPath = public_path('/forms/files');
        
        if (!file_exists($destinationPath)) {
          mkdir($destinationPath, 0777, true);
        }

        foreach ($data as $key => $value) {
             if ($request->hasFile($key)) {
                $file = request()->$key;
                $file_name = str_random(10).'-'.time().'.'.$file->extension();
                $file->move($destinationPath, $file_name);
                $data[$key] = $file_name;
            }
        }
        
        $data['user_id']     = $user_id;
        $data['schedule_id'] = $schedule_id;
        $data['created_at']  = \Carbon\Carbon::now();
        $data['updated_at']  = \Carbon\Carbon::now();

        $submittedData = array(
              'schedule_id'   => $schedule_id,
              'user_id'       => $user_id,
              'form_id'       => $form_id,
              'submit_status' => 1,
            );

        DB::table($table)->where(['user_id' => $user_id , 'schedule_id' => $schedule_id])->delete();

        $insertID  = DB::table($table)->insert($data);
        
        $preDataCount = DB::table('user_form_submitted')
                 ->where(['user_id' => $user_id , 'form_id' => $form_id , 'schedule_id' => $schedule_id])
                 ->count();

        if(empty($preDataCount) || is_null($preDataCount) || count($preDataCount) <= 0 ){
           DB::table('user_form_submitted')->insert($submittedData);
        }

        if($insertID){

            if($save_record_as != 1){
                DB::table('user_form_submitted')->where(['user_id' => $user_id , 'schedule_id' => $schedule_id  , 'form_id' => $form_id ])->update(['submit_status' => 0]);
                return ['status' => true , 'message' => 'Successfully record save as draft'];
            }
        
        if(!empty($preDataCount) && !is_null($preDataCount) && count($preDataCount) > 0 ){  

           DB::table('user_form_submitted')->where(['user_id' => $user_id , 'schedule_id' => $schedule_id  , 'form_id' => $form_id ])->update(['submit_status' => 1  , 'user_submission_request' => 0 , 'record_accept_status' => 0 , 'admin_resubmission_request' => 0 , 'user_resubmission_reason' => '' , 'admin_resubmission_reason' => '']);
           return redirect('admin/form')->with('status',true)->with('msg','Successfully resubmitted record');


        }

            return redirect('admin/form')->with('status',true)->with('msg','Successfully submitted record');
        }else{
            return $this->returnResponse(false,'Failed to add record','data-list?id='.$form_id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    { 
        $form_id     = $request->form_id;
        $schedule_id = $request->schedule_id;
        $user_id     = $request->user_id;
        $role        = Auth::user()->role;

        if($role == 1){
          $user_id  = base64_decode($request->user_id);
        }else{
          $user_id  = Auth::id();
        }

        if(!empty($form_id) && !empty($form_id)){
            $form_id = base64_decode($form_id);
        }
        
        if(!empty($schedule_id) && !empty($schedule_id)){
            $schedule_id = base64_decode($schedule_id);
        }
          
        $table = $this->getTableName($form_id);

        $data['form_name'] = $table;
        
        $table = $table.'_'.$form_id;


        $where = array(
           'user_id'     => $user_id,
           'schedule_id' => $schedule_id,
        );

        $data['formData']    = DB::table($table)->where($where)->first();

        $data['form']        = Form::find($form_id);
     
        $data['field_types'] = FieldTypes::all();

        $data['form_fields'] = FormField::where('form_id',$form_id)
                                   ->WhereNotIn('field_title',['ID','Created Date','Last Modified'])
                                   ->get();
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


        foreach ($data['formData'] as $key2 => $value2) {
              foreach ($data['form_fields'] as $key3 => $value3) {
                    if(strtolower($value3->field_name) == strtolower($key2)){
                       $data['form_fields'][$key3]->field_value = $value2;   
                    }
              }
        }

        foreach ($data['field_types'] as $key1 => $value1) {
                
                foreach ($data['form_fields'] as $key2 => $value2) {
                        if($value1->id == $value2->field_type_id){
                           $data['form_fields'][$key2]->field_type = $value1->field_type_identifier;
                        }
                }

        }
        
        return view('formdata.show',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $form_id = $request->form_id;
        $data_id = $request->data_id;

        $table = $this->getTableName($form_id);

        $data['form_name'] = $table;

        $data['formData']    = DB::table($table)->where('id',$data_id)->first();

        $data['form']        = Form::find($form_id);
     
        $data['field_types'] = FieldTypes::all();

        $data['form_fields'] = FormField::where('form_id',$form_id)
                                   ->WhereNotIn('field_title',['ID','Created Date','Last Modified'])
                                   ->get();

        foreach ($data['formData'] as $key2 => $value2) {
              foreach ($data['form_fields'] as $key3 => $value3) {
                    if($value3->field_name == $key2){
                       $data['form_fields'][$key3]->field_value = $value2;   
                    }
              }
        }

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

        return view('formdata.edit',compact('data'));

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
        
        $form_id = $request->form_id;
        $data_id = $request->data_id;

        $data = $request->except('_token' , 'form_id' , 'data_id');

        foreach ($data as $key => $value) {
            $type = gettype($value);
            if($type == 'array'){
                $data[$key] = implode(',', $value);    
            }

            $input_fields[] = $key;
        }
        
        $validate = SetRules::setRules($form_id,$input_fields,$data_id); // input_fields in array formate

        $request->validate($validate);

        $destinationPath = public_path('/forms/files');
        
        if (!file_exists($destinationPath)) {
          mkdir($destinationPath, 0777, true);
        }

        $table = $this->getTableName($form_id);
         
        $newFile = array();
        $oldFile = array();

        foreach ($data as $key => $value) {
             if ($request->hasFile($key)) {
                $file = request()->$key;
                if(!empty($file)){
                //   $oldFile[] = DB::table($table)->select($key)->where('id',$data_id)->get();
                    $file_name = str_random(10).'-'.time().'.'.$file->extension();
                    $file->move($destinationPath, $file_name);
                    $data[$key] = $file_name;
                    $newFile[]  = $file_name;
                }
            }
        }

        $data['updated_at'] = \Carbon\Carbon::now();

        DB::table($table)->where('id',$data_id)->update($data);

        if(true){

            return $this->returnResponse(true,'Successfully updated record','data-list?id='.$form_id);
        }else{
            return $this->returnResponse(false,'Failed to update record','data-list?id='.$form_id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy(Request $request){

       $data_id = $request->data_id;
       $form_id = $request->form_id;

       $table = $this->getTableName($form_id);

       $user = DB::table($table)->where('id',$data_id)->delete();

       if($user){
           $arr = array('status' => true , 'message' => 'Successfully deleted record');
           return response($arr);
       }else{
          $arr = array('status' => false , 'message' => 'Failed to delete record');
           return response($arr);
       }

    }
    public function getTableColumns($table)
    {

        return DB::getSchemaBuilder()->getColumnListing($table);
        
        
    }

     public function returnResponse($status , $message , $redirect ){
       
       return redirect($redirect)->with('msg' , $message)->with('status' , $status );

    }

    public function getTableName($id){

      // $Form      = Form::find($id);
      // $table     = str_replace(' ', '_', strtolower($Form->name));
      $Form      = DB::table('forms')->where('id',$id)->first();
      $table     = str_replace(' ', '_', strtolower($Form->name));

      return trim($table);
    }

    public function getRules($form_id, $field_name){


       if(!is_null($form_id) && !is_null($field_name)){

           $rules = DB::table('form_fields')
                       ->select('rules')
                       ->where('form_id' , $form_id)
                       ->where('field_name' , $field_name)
                       ->first();

           if(!empty($rules)){
                       $rules      = unserialize($rules->rules);
                       return $rules ?? false;
           }else{
             return false;
           }

        }else{
           
             return false;
        }
    }

    public function setRules($table = null,$field = null){

        $rules = $this->getRules($table, $field);

        $table_name = $this->getTableName($table);

        if(!empty($rules)){
            
            $str = null;
            
            foreach($rules as $key => $rule){
                switch ($key) {
                    case 'validation':
                          if($rule == 1)
                              $str .= 'required|';
                          if($rule == 2){
                             $str .= 'unique:'.$table_name.','.$field.'|';
                             return $str;
                          }
                        break;
                        case 'min':
                          if(!empty($rule) && !is_null($rule) && $rule != 0)
                              $str .= 'min:'.$rule.'|';
                        break;
                        case 'max':
                          if(!empty($rule) && !is_null($rule) && $rule != 0)
                              $str .= 'max:'.$rule.'|';
                        break;
                    
                    default:
                             $str;
                        break;
                }
           }

           if(!empty($str)){
             $str = str_replace('||', '|', $str);
             if(substr($str, -1) == '|' ){
                // return substr($str, 0, -1);
                return $field .'=>'.$str;
             }else{
                 return trim($str);
             }
           }

           return false;
        }

        return false;

    }

    public function readFormNotifications($id = null , $schedule = null){
        if(!is_null($id) && !empty($id) && !is_null($schedule) && !empty($schedule)){
              $userReads = DB::table('form_scheduled_forms')
                             ->join('form_schedules' , 'form_scheduled_forms.schedule_id' , '=' , 'form_schedules.id')
                             ->select('user_readed')
                             ->where('form_id' , $id)
                             ->whereDate('form_schedules.start_date', '<=' , date('Y-m-d'))
                             ->where('schedule_id',$schedule)
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

    function export(Request $request){
      
      $user_id     = base64_decode($request->user_id);
      $form_id     = base64_decode($request->form_id);
      $schedule_id = base64_decode($request->schedule_id);
      $formate     = $request->formate;

      if(auth::user()->role != 1){
          $user_id = auth::id();
      }

      switch ($formate) {
          case 'excel':
                $formate = 'data.xlsx';
          break;
           case 'pdf':
                $formate = 'data.pdf';
          break;
          default:
                $formate = 'data.csv';
              break;
      }

      $table = $this->getTableName($form_id);

      $table = $table.'_'.$form_id;

      $data['columns'] = $this->getTableColumns($table);

      foreach ($data['columns'] as $key => $value) {
          if($value == 'id' || $value == 'status' || $value == 'user_id' || $value == 'schedule_id' || $value == 'updated_at' || $value == 'created_at')
               unset($data['columns'][$key]);
      }

      $data['data']    = DB::table($table)->where([['user_id',$user_id] , ['schedule_id',$schedule_id ]])->get();

       return Excel::download(new FormDataExport($data) , $formate);

    }
}