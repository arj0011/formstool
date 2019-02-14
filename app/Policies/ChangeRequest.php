<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use DB;

class ChangeRequest
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    protected $index  =21;
    protected $create  = 22;
    
    public function __construct()
    {
        //
    }
    public function index(User $user)
    {  
        return $this->getPermission($user , $this->index);
    }
    public function create(User $user)
    {
       return $this->getPermission($user , $this->create);
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
