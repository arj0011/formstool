<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';

    /**
     * Get the permission record associated with the module.
     */
    
   public function permissions()
    {
    	return $this->hasMany('App\Permission');
    }
}
