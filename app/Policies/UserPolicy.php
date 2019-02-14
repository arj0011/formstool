<?php

namespace App\Policies;

use DB;
use App\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    protected $index      = 1;
    protected $create     = 2;
    protected $update     = 3;
    protected $delete     = 4;
    protected $status     = 5;


    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function index(User $user)
    {  
        return $this->getPermission($user , $this->index);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $this->getPermission($user , $this->create);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user)
    {
        return $this->getPermission($user , $this->update);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user)
    {
        return $this->getPermission($user , $this->delete);
    }

    /**
     * Determine whether the user can change status the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function status(User $user)
    {
        return $this->getPermission($user , $this->status);
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
