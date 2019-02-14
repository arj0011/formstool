<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Form;

class SetRules extends Controller
{
    
    public static function setRules($table = null , $fields = null , $data_id = null){

    	if(!is_null($table) && !empty($table) && !is_null($fields) && !empty($fields)){
            
            $rules = self::getRules($table, $fields,$data_id);

            $table_name = self::getTableName($table);

            return self::rules($table_name,$rules,$data_id);

    	}

    	return false;

    }

     public static function getRules($form_id, $field_names){


       if(!is_null($form_id) && !empty($form_id) && !is_null($field_names) && !empty($field_names)){

           $rules = DB::table('form_fields')
                       ->select('id','field_name','rules' , 'field_type_id')
                       ->where('form_id' , $form_id)
                       ->whereIn('field_name' , $field_names)
                       ->get();

           if(!empty($rules)){
           	    foreach ($rules as $key => $rule) {
                    $rules[$key]->rules  = unserialize($rule->rules);
           	    }

                return $rules ?? false;
           }

           return false;

        }

        return false;
    }


    public static function rules($table_name = null , $rules = null,$data_id = null){

    	 if(!empty($rules) && !is_null($rules) && !empty($table_name) && !is_null($table_name)){
            
            $validationArray = array();
            
            foreach($rules as $value){
                
                 $str = null;

                 $field = $value->field_name;
                 $field_type_id = $value->field_type_id;
            	
            	 foreach ($value->rules as $key => $rule ) {
	                switch ($key) {
	                    case 'validation':
	                          if($rule == 1)
	                              $str .= 'required|';
	                          if($rule == 2){
                               if(!empty($data_id) && !is_null($data_id)){
                                  $str .= 'unique:'.$table_name.','.$field.','.$data_id.'|';
                               }else{
	                                $str .= 'unique:'.$table_name.','.$field.'|';
                               }
	                          }
	                        break;
	                        case 'min':
	                          if(!empty($rule) && !is_null($rule) && $rule != 0){
	                            if($field_type_id == 8)
                                $str .= 'min:'.$rule.'|integer|';
                              else
                                $str .= 'min:'.$rule.'|';
                                
                            }
	                        break;
	                        case 'max':
	                          if(!empty($rule) && !is_null($rule) && $rule != 0){
                              if($field_type_id == 8)
                                $str .= 'max:'.$rule.'|integer|';
                              else
	                              $str .= 'max:'.$rule.'|';
                            }
	                        break;
	                    
	                    default:
	                             $str;
	                        break;
                    }
            	 }

	               if(!empty($str)){
		             
		             $str = str_replace('||', '|', $str);
		             
		             if(substr($str, -1) == '|' ){
		             	$validationArray[$field] = substr($str, 0, -1);
		             }

		           }
           }

           return $validationArray;
        }
          return false;
    }

      public static function getTableName($id){

        // $Form      = Form::find($id);
        // $table     = str_replace(' ', '_', strtolower($Form->name));
        
        $Form      = DB::table('forms')->where('id',$id)->first();
        $table     = str_replace(' ', '_', strtolower($Form->name));

        if($Form->form_type == 'Vertical'){
          return trim($table).'_'.$id;
        }
            return trim($table);
    }

}


