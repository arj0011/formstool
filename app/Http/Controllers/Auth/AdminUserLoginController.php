<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;

class AdminUserLoginController extends Controller
{

    public function login(Request $request){
            
            $id   = $request->id;

            $adminAuth = Auth::id();

            Auth::logout();

           if(Auth::loginUsingId($id)){
           	    Session()->put('adminAuth', $adminAuth);
			    return redirect()->intended('dashboard')->with('msg' , 'Successfully login by '.auth::user()->first_name.' '.auth::user()->last_name)->with('status' , true);
			}
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $lastLoginId = Auth::id();
        
        Auth::logout();

        if(Auth::loginUsingId(Session()->get('adminAuth'))){
             Session()->pull('adminAuth');
        	 return redirect()->intended('dashboard');
        }
    }
}
