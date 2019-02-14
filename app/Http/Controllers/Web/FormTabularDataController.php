<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\FormTable;
use Validator;
use App\Form;
use App\User;
use DB;
use App\FormField;
use App\FieldTypes;
use App\Exports\TabularExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Web\ExportController;
use Illuminate\Support\Arr;
use Auth;
use App\Mail\FormResubmitMail;
use Mail;
class FormTabularDataController extends Controller
{

  public function index(Request $request){
        $user=Auth::user();
        $user_id=$request->has('user_id')?base64_decode($request->user_id):0;
        $schedule_id=$request->has('schedule_id')?base64_decode($request->schedule_id):0;
        $data=[];
        $form_id=base64_decode($request->id);
        $form=Form::find($form_id);
        if(!empty($form)){
          if($form->form_type == 'Vertical'){
            $vertical_table=str_replace(' ', '_', strtolower($form->name))."_".$form_id;
            if(Schema::hasTable($vertical_table)){
              $record = DB::table($vertical_table)
                          ->select($vertical_table.'.*','u.first_name','u.last_name')
                          ->leftjoin('users as u','u.id','=',$vertical_table.'.user_id')
                          ->where(function($query)use($user_id,$schedule_id,$vertical_table){
                              if($user_id)  $query->where($vertical_table.'.user_id','=',$user_id);
                              if($schedule_id)  $query->where($vertical_table.'.schedule_id','=',$schedule_id);
                            }) 
                          ->get();
              if($record) 
                $record = $record->toArray();
              else 
                $record=[];

              $columns=$this->getTableColumns($vertical_table);
              $columns=array_where($columns, function ($value, $key) {
                if(!in_array($value,array('id','user_id','status','created_at','updated_at'))){
                  return $value;
                }
              });
              $push_arr=[
                "table_data"=>$record,
                "columns"=>$columns,
                "table_id"=>'',
                "table_titles"=>[],
                "row_labels"=>'',
                "table_name"=>'',
                "label_heading"=>''
              ];
              array_push($data,$push_arr);
              return $data;
          }
        }
      }

        $tables  = FormTable::where('form_id',$form_id)->get();
        if($tables)
        {
            foreach($tables as $key=>$value)
              {
                  $table_id=$value->id;  
                  $table_name=$this->getTableName($form_id,$table_id);
                  if(Schema::hasTable($table_name)){
                     $record=DB::table($table_name)
                          ->select($table_name.'.*','u.first_name','u.last_name')
                          ->leftjoin('users as u','u.id','=',$table_name.'.user_id')
                          ->where(function($query)use($user_id,$schedule_id,$table_name){
                              if($user_id)  $query->where($table_name.'.user_id','=',$user_id);
                              if($schedule_id)  $query->where($table_name.'.schedule_id','=',$schedule_id);
                            }) 
                          ->get();
                     if($record) $record=$record->toArray();
                       else $record=[];
                        $columns=$this->getTableColumns($table_name);
                        $columns=array_where($columns, function ($value, $key) {
                                    if(!in_array($value,array('id','user_id','status','created_at','updated_at')))
                                    {
                                        return $value;
                                    }
                        });
                                $push_arr=[
                                "table_data"=>$record,
                                "columns"=>$columns,
                                "table_id"=>$table_id,
                                "table_titles"=>json_decode($value->table_titles),
                                "row_labels"=>json_decode($value->row_data),
                                "table_name"=>$table_name,
                                "label_heading"=>!empty($value->label_heading)?$value->label_heading:"#"
                             ];
                     array_push($data,$push_arr);
                  }

            }
         $form_name=isset($form->name)?$form->name:'';  
        } 
    if($request->has('export')){
         return $data;
        }
    return view('tabulardata.index',compact('data','form_name'));
 }
 public function edit(Request $request)
    {
        $data=[];
        $form='';
        $input=$request->only('table_id','table_name','data_id');
        $table_id=base64_decode($input['table_id']);
        $data_id=base64_decode($input['data_id']);
        $table=FormTable::find($table_id);
        if($table && Schema::hasTable($input['table_name']))
        {
         $form=Form::find($table->form_id); 
         $columns=$this->getTableColumns($input['table_name']);
         if(count($columns)>0)
         {
         // $columns[$table->label_heading];
         // unset($columns['row_label']);
         }
         $row_data=DB::table($input['table_name'])->where('id',$data_id)
                                         ->first();
         $form_fields= FormField::with('field_type')
                                      ->where('table_id',$table_id)
                                      ->WhereNotIn('field_title',['ID','Created Date','Last Modified'])
                                     ->get();
         $data=[
                "row_data"=>$row_data,
                "columns"=>$columns,
                "table_id"=>$table_id,
                "table_titles"=>json_decode($table->table_titles),
                "form_fields"=>count($form_fields)>0?$form_fields->toArray():[],
                "label_headig"=>$table->label_heading
               ];
        $data['form_name']=isset($form->name)?$form->name:''; 
       }else return back()->with(["msg"=>"Table does't exist.","status"=>false]);
  return view('tabulardata.edit',compact('data'));
}
public function update(Request $request)
{
  $input=$request->all();
  $validator=Validator::make($input,[
            'table_name' =>'required',
            'data_id' => 'required',
            ]);
  if($validator->fails()){
           return back()->withErrors($validator);
        }else{
         $table_name=trim($input['table_name']);
         $data_id=base64_decode($input['data_id']);
         if(Schema::hasTable($table_name)) 
         {
            $res=DB::table($table_name)
                    ->where('id',$data_id)
                    ->update($request->except('data_id','table_name','table_id','_token'));
            if($res)return back()->with(['msg'=>"Record updated successfully!!.","status"=>true]);
             else return back()->with(['msg'=>"Failded to update record.","status"=>false]);
         }else return back()->with(['msg'=>"Table does't exist.","status"=>false]);
       }

    
}
  public function destroy(Request $request){

       $data_id     = $request->data_id;
       $table_name  = $request->table_name;
     if(Schema::hasTable($table_name)) 
     {
       $res=DB::table($table_name)->where('id',$data_id)->delete();
      if($res){
           $arr = array('status' => true , 'message' => 'Successfully deleted record');
           return response($arr);
       }else{
          $arr = array('status' => false , 'message' => 'Failed to delete record');
           return response($arr);
       }
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
 public function getTableName($form_id,$table_id){
         $Form      = Form::find($form_id);
         if($Form) $table=str_replace(' ', '_', strtolower($Form->name))."_".$form_id."_".$table_id;
         else  $table='';
         return $table;
    }
    public function export(Request $request) 
    {
     $type=isset($request->type)?$request->type:'';
     $file_name='';
     switch($type)
     {
      case "csv": $file_name="table.csv";
            break;
      case "excel":$file_name="table.xlsx";
        break;
      default:
           $file_name="table.xlsx";
      }
     $request->merge(['export' =>true]);
     $data=$this->index($request);
     return Excel::download(new TabularExport($data),$file_name);
    }
    public function printTable(Request $request)
    {
        
    }
   public function updateStatus(Request $request)
   {
    $input=$request->all();
    $validator=Validator::make($input,[
            'user_id' =>'required',
            'form_id' => 'required',
            "status"=>'required',
            ]);
  if($validator->fails()){
           return back()->withErrors($validator);
        }else{
        $user_id=base64_decode($input['user_id']);
        $form_id=$input['form_id'];
        $user=User::find($user_id);
        $form=Form::find($form_id);
        $status=base64_decode($input['status']); 
        $tables=FormTable::where('form_id',$form_id)->get();
        DB::beginTransaction();
        try{
              if($tables)
              {
                  foreach($tables as $key=>$value)
                    {
                        $table_id=$value->id;  
                        $table_name=$this->getTableName($form_id,$table_id);
                        if(Schema::hasTable($table_name)){
                           $res=DB::table($table_name)->where('user_id',$user_id)
                          ->update(['status'=>$status]);
                        }

                  }
              } 
          DB::commit();
          if($status==4){
            if($user && $form)
            {
                $email=$user->email;
                $data['form']=$form->name;
                $data['first_name']=$user->first_name;
                Mail::to($email)->send(new FormResubmitMail($data));
            }
          }
          return back()->with(['msg'=>"Status has been changed successfully!!.","status"=>true]);
            // all good
        }catch(\Exception $e){
            DB::rollback();
            //something went wrong
             return back()->with(['msg'=>"Failed to update status!!.","status"=>false]);
        } 
   }
   }
    public function show(Request $request)
    {
        $data=[];
        $form='';
        $input=$request->only('table_id','table_name','data_id');
        $table_id=base64_decode($input['table_id']);
        $data_id=base64_decode($input['data_id']);
        $table=FormTable::find($table_id);
        if($table && Schema::hasTable($input['table_name']))
        {
            
         $form=Form::find($table->form_id); 
         $columns=$this->getTableColumns($input['table_name']);
         if(count($columns)>0)
         {
         // $columns[$table->label_heading];
         // unset($columns['row_label']);
         }
         $row_data=DB::table($input['table_name'])->where('id',$data_id)
                                         ->first();
         $form_fields= FormField::with('field_type')
                                      ->where('table_id',$table_id)
                                      ->WhereNotIn('field_title',['ID','Created Date','Last Modified'])
                                     ->get();
         $data=[
                "row_data"=>$row_data,
                "columns"=>$columns,
                "table_id"=>$table_id,
                "table_titles"=>json_decode($table->table_titles),
                "form_fields"=>count($form_fields)>0?$form_fields->toArray():[],
                "label_headig"=>$table->label_heading,
                "show_only"=>true
               ];
        $data['form_name']=isset($form->name)?$form->name:''; 
       }else return back()->with(["msg"=>"Table does't exist.","status"=>false]);
  return view('tabulardata.edit',compact('data'));
}
}
