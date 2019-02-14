<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FieldTypes extends Model
{
    //

    protected $table="field_types";

     public function form_fields()
    {
        return $this->belongsTo('App\FormField');
    }
}
