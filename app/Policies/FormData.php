<?php

namespace App\Policies;

use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormData
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    protected $viewSubmission=23;

    
    public function __construct()
    {
        //
    }
    public function viewSubmission(User $user)
    {  
        return $this->getPermission($user,$this->viewSubmission);
    }
   
    protected function getPermission($user,$p_id)
    {   
        $status = false;

        $permissions = DB::table('permission_role as p_role')
                             ->where('role_id' , '=' , $user->role)
                             ->where('permission_id' , '=' , $p_id)
                             ->count();

        if(!empty($permissions) && !is_null($permissions) && $permissions > 0){
            return true;
        }
     
        return $status;
    }
}
