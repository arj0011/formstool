<?php

namespace App\Providers;


use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\User;
use App\Form;
use App\Role;
use App\UserGroup;
use App\UserRequest;
use App\FormTable;;

use App\Policies\UserPolicy;
use App\Policies\FormPolicy;
use App\Policies\RolePolicy;
use App\Policies\GroupPolicy;
use App\Policies\ChangeRequest;
use App\Policies\FormData;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies=[
               'App\Model' => 'App\Policies\ModelPolicy',
                User::class =>  UserPolicy::class,
                Form::class =>  FormPolicy::class,
                Role::class =>  RolePolicy::class,
                UserGroup::class => GroupPolicy::class,
                UserRequest::class=>ChangeRequest::class,
                FormTable::class=>FormData::class,
              
            ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
