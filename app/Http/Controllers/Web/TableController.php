<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use DB;

class TableController extends Controller
{   
    
    private static $table  = null;
    private static $column = null;


    public function __construct($table_name = null , $column_name = null){
         parent::__construct();

         $this->table       = $table_name;
         $this->column      = $column_name;
    }

    public static function createTable($table_name, $fields = [])
    {
     
        Schema::create($table_name, function (Blueprint $table) use ($fields, $table_name) {
            $table->increments('id');
            if (count($fields)>0){
                foreach ($fields as $field){
                    if(isset($field['default'])){
                      if(isset($field['comment']))
                        $table->{$field['type']}($field['name'])->default($field['default'])->comment($field['comment']);
                       else $table->{$field['type']}($field['name'])->default($field['default']);

                        }
                       else{
                           $table->{$field['type']}($field['name'])->nullable();
                          }
                }   
            }
          $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
          $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        return response()->json(['message' => 'Given table has been successfully created!'], 200);
    }

public static function updateTable($table_name, $fields = [])
    {
        Schema::table($table_name, function (Blueprint $table) use ($fields, $table_name) {
            
            if (count($fields)>0){
                
                foreach($fields as $field) {
                   if(!Schema::hasColumn($table_name,$field['name']))
                   {
                    $table->{$field['type']}($field['name'])->nullable();
                   }else return false;
                }
            }
           
        });

        return response()->json(['message' => 'Given table has been successfully created!'], 200);
    }
    
public static function removeTable($table_name,$column_name = null)
    {
        Schema::dropIfExists($table_name); 
        
        return true;
    }

    public static function dropColumn($table_name,$column_name){

         Schema::table($table_name , function (Blueprint $table) use ($table_name, $column_name)
         {
                $table->dropColumn($column_name);

                return true;
         });

            return response()->json(['message' => 'Table column not exist'] , 200);
    }

    public static function renameColumn($table_name, $old_column , $new_column){
       
         Schema::table( $table_name, function (Blueprint $table) use ($old_column, $new_column)
         {

            $table->renameColumn($old_column, $new_column);

            return true;

         });
    }

    public static function renameTable($oldTableName, $newTableName){
       
          Schema::rename($oldTableName, $newTableName);

          return true;
    }

}
