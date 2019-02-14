<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;

class ProfileController extends Controller
{
  
   public function show(Request $request){

   	    $data['user'] = User::select('first_name' , 'last_name' , 'email' , 'mobile' , 'gender' , 'profile_image')
	                          ->where('id' , auth::id())
	                          ->first();

   	    if(!empty($data['user'])){
   	    	if(empty($data['user']->profile_image) || is_null($data['user']->profile_image) || !file_exists('public/images/profile_images/'.$data['user']->profile_image))
   	    		 $data['user']->profile_image = 'image_not_available.jpeg';
   	    }

   	    return view('profile.show',compact('data'));
        
   }

   public function update(Request $request){

   	    $id            =  auth::id();
   	    $first_name    =  $request->first_name;
   	    $last_name     =  $request->last_name;
   	    $mobile        =  $request->mobile;
   	    $gender        =  $request->gender;
   	    $profile_image =  $request->profile_image;

		$validatedData = $request->validate([
			'first_name'    => 'required|min:2|max:50',
			'last_name'     => 'required|min:2|max:50',
			'mobile'        => 'required|digits_between:10,12|unique:users,mobile,'.$id,
			'gender'        => 'required',
			'profile_image' =>  'image|mimes:jpeg,jpg,png'
		]);

		$old_image = User::select('profile_image')->where('id' , $id)->first();

        $image = '';
        if ($request->hasFile('profile_image')) {
            $profileImage = str_random('10').'.'.time().'.'.request()->profile_image->getClientOriginalExtension();
            request()->profile_image->move(public_path('images/profile_images'), $profileImage);
            $image = $profileImage;
        }

		$user = User::find($id);
				$user->first_name = $first_name;
				$user->last_name  = $last_name;
				$user->mobile     = $mobile;
			 	$user->gender     = $gender;
			 	if(!empty($image)){$user->profile_image  = $image;}

		if($user->save()){

      if(isset($old_image->profile_image)){
  			if(!empty($image) && file_exists('public/images/profile_images/'.$old_image->profile_image)){
  				unlink('public/images/profile_images/'.$old_image->profile_image);
  			}

      }
      return $this->returnResponse(true,'Successfully Updated Profile', 'profile');
		}else{
			if(!empty($image) && file_exists('public/images/profile_images/'.$image)){
				unlink('public/images/profile_images/'.$image);
		}
          return $this->returnResponse(false,'Failed To Update Profile', 'profile');
		}
   }

   public function changePassword(){
       return view('profile.change_password');
   }

   public function updatePassword(Request $request){
       
       $id = auth::id();
       $current_password          = $request->current_password;
       $new_password              = $request->new_password;
       $password_confirmation     = $request->password_confirmation;

       $request->validate([
        'current_password'      => 'required|min:6',
        'new_password'          => 'min:6|required_with:password_confirmation|same:password_confirmation',
        'password_confirmation' => 'min:6'
      ]);


        if (!(Hash::check($current_password, Auth::user()->password))) {
          return $this->returnResponse(false,'Your current password does not matches with the password that you have provided, please try again', 'change-password');
        }
      
       $user = User::find($id);
                $user->password = Hash::make($new_password);
                     
        if($user->save()){
          return $this->returnResponse(true,'Successfully Updated Password', 'change-password');
        }else{
          return $this->returnResponse(false,'Failed To Update Password', 'change-password');
        }

   }

    public function returnResponse($status , $message , $redirect ){
       
       if($status)
       	  $color = 'success';
       	else
       	  $color = 'danger';

       return redirect($redirect)->with('msg' , $message)->with('color' , $color );

    }
}
