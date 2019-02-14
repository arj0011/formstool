<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    //  
    protected $table="form_fields";

    protected $fillable=['form_id','list_order','field_name','field_type_id','field_title','col_name'];

    public function field_type()
    {
    	return $this->belongsTo('App\FieldTypes','field_type_id','id');
    }

    public function getFieldNameAttribute($value)
    {
        return ucwords($value);
    }

}
