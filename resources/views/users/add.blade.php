@extends('layouts.app')
@section('content')   
     <!-- Content Header (Page header) -->
    <section class="content-header">
      @if(isset($data['user']))
      <h1>
       Edit User
      </h1>
          {{ Breadcrumbs::render('edit-user') }}
      @else
      <h1>
       Add User
      </h1>
          {{ Breadcrumbs::render('add-user') }}
      @endif
    </section>

    <!-- Main content -->
    <section class="content">

       @if (Session::has('msg'))
         <div class="row">
            <div class="col-md-10">
                <div  class="alert alert-{{ Session::get('color') }}">
                   <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                   <p>{{ Session::get('msg') }}</p>
                </div>
            </div>
          </div>
        @endif

    <div class="row">
      <div class="col-md-10 col-md-offset-1" >
          
        <!-- Default box -->
        <div class="box box-solid">
          <div class="box-header">
          </div>
          <form data-toggle="validator" class="form" action="{{ route('user/store') }}" method="POST" enctype="multipart/form-data" id="userForm">
            {{ csrf_field() }}
            @if(isset($data['user']))
                    {{ method_field('PUT') }}
              <input type="hidden" name="id" value="{{$data['user']->id}}">
            @else
                    {{ method_field('POST') }}
            @endif
            <div class="box-body">
              <div class="row">
          
                 <div class="col-md-8">
                    <div class="form-group">
                      <label for="first-name">First Name</label>
                      <input type="text" name="first_name" class="form-control " id="first-name" placeholder="first name" value="@if(old('first_name')) {{ old('first_name') }} @elseif(isset($data['user'])){{ $data['user']->first_name}}@endif" required>
                       <div class="help-block with-errors"></div>
                      @if ($errors->has('first_name'))
                        <span class="help-block text-red">
                          <strong>{{ $errors->first('first_name') }}</strong>
                        </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label for="last-name">Last Name</label>
                      <input type="text" name="last_name" class="form-control " id="last-name" placeholder="last name" value="@if(old('last_name')) {{ old('last_name') }} @elseif(isset($data['user'])){{ $data['user']->last_name}} @endif" required>
                       <div class="help-block with-errors"></div>
                      @if ($errors->has('last_name'))
                        <span class="help-block text-red">
                          <strong>{{ $errors->first('last_name') }}</strong>
                        </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label>Gender :</label>&nbsp;&nbsp;&nbsp;&nbsp;
                      <input type="radio" name="gender" value="Male" @if(old('gender')) @if(old('gender') == 'Male') checked @endif @elseif(isset($data['user']))
                        @if ($data['user']->gender == 'Male')
                          checked 
                        @endif
                    @endisset required>&nbsp;&nbsp;Male &nbsp;&nbsp;&nbsp;&nbsp;
                      <input type="radio" name="gender" value="Female" @if(old('gender')) @if(old('gender') == 'Female') checked @endif @elseif(isset($data['user']))
                        @if ($data['user']->gender == 'Female')
                          checked 
                        @endif
                    @endisset required>
                      &nbsp;&nbsp;Female
                      <div class="help-block with-errors"></div>
                      @if ($errors->has('gender'))
                      <span class="help-block text-red">
                      <strong>{{ $errors->first('gender') }}</strong>
                      </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label for="contact-number">Contact Number</label>
                      <input type="text" name="mobile" class="form-control numeric" id="contact-number" placeholder="contact number" value="@if(old('mobile')){{old('mobile')}}@elseif(isset($data['user'])){{$data['user']->mobile}}@endif" pattern="^[6-9]\d{9}$" data-error="Invalid mobile number" required>
                      <div class="help-block with-errors"></div>
                      @if ($errors->has('mobile'))
                        <span class="help-block text-red">
                          <strong>{{ $errors->first('mobile') }}</strong>
                        </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label for="email-address">Email Address</label>
                      <input type="email" name="email" class="form-control" id="email-address" placeholder="email address" value="@if(old('email')) {{ old('email') }} @elseif(isset($data['user'])){{ $data['user']->email}}@endif" required>
                      <div class="help-block with-errors"></div>
                      @if ($errors->has('email'))
                        <span class="help-block text-red">
                          <strong>{{ $errors->first('email') }}</strong>
                        </span>
                      @endif
                    </div>

                     <div class="form-group group-div">
                      <label for="group">District</label>
                      <select class="form-control" id="distric" name="distric" required>
                         @forelse ($data['districs'] as $key => $distric)
                           @if ($key == 0)
                              <option value="">--select district--</option>
                           @endif
                              <option @if(old('distric')) @if ( old('distric') == $distric->id) selected @endif @elseif(isset($data['user']) && $data['user']->distric_id == $distric->id)) selected @endif  value="{{$distric->id}}">{{$distric->distric}}</option>
                        @empty
                           <option value="">No any Group available</option>
                        @endforelse
                      </select>
                       <div class="help-block with-errors"></div>
                        @if ($errors->has('group'))
                        <span class="help-block text-red">
                          <strong>{{ $errors->first('group') }}</strong>
                        </span>
                      @endif
                    </div>

                    <div class="form-group group-div">
                      <label for="group">Group</label>
                      <select class="form-control" id="group" name="group" required>
                         @forelse ($data['groups'] as $key => $group)
                           @if ($key == 0)
                              <option value="">--select group--</option>
                           @endif
                              <option @if(old('group')) @if ( old('group') == $group->id) selected @endif @elseif(isset($data['user']) && $data['user']->group == $group->id)) selected @endif  value="{{$group->id}}">{{$group->group_name}}</option>
                        @empty
                           <option value="">No any Group available</option>
                        @endforelse
                      </select>
                       <div class="help-block with-errors"></div>
                        @if ($errors->has('group'))
                        <span class="help-block text-red">
                          <strong>{{ $errors->first('group') }}</strong>
                        </span>
                      @endif
                    </div>
                    
                     <div class="form-group" id="role-div">
                      <label for="role">Role</label>
                      <select class="form-control" id="role" name="role" required>
                        @forelse ($data['roles'] as $key => $role)
                           @if ($key == 0)
                              <option value="">--select role--</option>
                           @endif
<option @if(old('role')) @if ( old('role') == $role->id) selected @endif @elseif(isset($data['user']) && $data['user']->role == $role->id)) selected @endif value="{{ $role->id }}">{{ $role->name }}</option>
                        @empty
                           <option value="">No any role available</option>
                        @endforelse
                      </select>
                       @if ($errors->has('role'))
                        <span class="help-block text-red">
                          <strong>{{ $errors->first('role') }}</strong>
                        </span>
                      @endif
                       <div class="help-block with-errors"></div>
                    </div>


                    <div class="form-group">
                      <label for="role">High Authority User</label>
                      <div class="row">
                        <div class="high-authority-email-container">
                        </div>
                         <div class="item add-more-container">
                          <div class="col-md-10">
                           <div class="form-group">
                            <input type="email" name="high_authority_user[]" class="form-control" id="authority-email" placeholder="Authority email">
                           </div>
                          </div>
                          <div class="col-md-2 text-right">
                            <button class="btn btn-default" id="addInput">Add More</button>
                          </div>
                         </div>
                      </div>
                      <div class="help-block with-errors"></div>
                    @foreach ($errors->get('high_authority_user.*') as $key => $message)
                      <span class="help-block text-red">
                        @php
                          print_r($message[0]);
                        @endphp
                      </span>
                    @endforeach
                    </div>

                    @if(!isset($data['user']))
                      <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="password" data-minlength="6" value="" required>
                       <div class="help-block">Minimum of 6 characters</div>
                        @if ($errors->has('password'))
                          <span class="help-block text-red">
                            <strong>{{ $errors->first('password') }}</strong>
                          </span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="confirm password" data-match-error="Password not match" value="" data-match="#password" required>
                         <div class="help-block with-errors"></div>
                        @if ($errors->has('password'))
                          <span class="help-block text-red">
                            <strong>{{ $errors->first('password') }}</strong>
                          </span>
                        @endif
                      </div>
                       <div class="form-group">
                        <input type="checkbox" name="notify" id="notify" value="1">
                        Notify to user by email for registration.
                      </div>
                    @endif
                 </div>
                 <div class="col-md-4">
                  <div class="form-group text-center">
                  <label>Profile Image</label><br>
                      <?php  if(isset($data['user'])){ ?>
                        <img id="image_view" src="{{ asset('public/images/profile_images/'.$data['user']->profile_image)}}" width="35%" height="100px">
                      <?php }else{ ?>
                        <img id="image_view" src="{{ asset('public/images/profile_images/image_not_available.jpeg')}}" width="35%" height="100px">
                      <?php } ?>
                      <label for="profile_image" class="btn btn-default btn-flat btn-block" style="width: 40%;margin-left:100px;"><i style="color:#3a88bc">Choose Image</i></label>
                      <input type="file" name="profile_image" id="profile_image">
                       @if ($errors->has('profile_image'))
                         <span class="help-block text-red">
                        <strong>{{ $errors->first('profile_image') }}</strong>
                        </span>
                        @endif
                   </div>
                 </div>
              </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <input id="submit-btn" type="submit" value="@if(isset($data['user'])) Update @else Add @endif" class="btn btn-success">
            </div>
            <!-- /.box-footer-->
          </form>
          </div>
           <div class="col-md-12 text-right" style="margin-bottom: 10px;">
          <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
          </div>
          </div>
          </div>
           
    </section>
    <!-- /.content -->
@endsection
@section('css-script')
  <!-- bootstrap validatior css -->
 <link rel="stylesheet" href="{{ asset('public/bootstrap/css/bootstrapValidator.min.css') }}">
 <style type="text/css">
    #profile_image{
         display: none;
   }

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.loader{
   position : absolute;
   left : 50%;
   top : 50%;
   transform : translate(50%,50%);
}

 </style>
@endsection
@section('js-script')
<!-- bootstrap validator js -->
<script src="{{ asset('public/bootstrap/js/bootstrapValidator.min.js')}}"></script>
 <script type="text/javascript">
   
   $('#role-div').hide();

   $('#profile_image').change(function (event) {
       var tmppath = URL.createObjectURL(event.target.files[0]);
       $('#image_view').attr('src',tmppath);
   });

   if($('#group').val()){
      let group = $('#group').val();

        if(group){

          let assignRole  = '';
          @if (isset($data['user']->role))
              assignRole  = '{{ $data['user']->role}}';
          @endif
          
            $.ajax({
              "headers":{
              'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
              },
            'type':'get',
            'url' : '{{ route('user/getRolesByGroup') }}',
            'data' : {id : group},
            'success' : function(response){
              if(response.status){
                $('.group-div').show();
                let html  = '';
                    html += '<option value="">-- select --</option>';
                response.data.map(function(item,index){
                      let selected = '';
                     if( assignRole == item.id)
                          selected = 'selected';
                     html += '<option value="'+item.id+'" '+selected+'>'+item.name+'</option>';
                });
               $("#role").html(html);
               $('#role-div').show();
              }else{
               // swal(response.message , "danger");
              }
            },
            'error' : function(error){
               console.log('Something went wrong');
              }
            });
      }
   }   
   

   $('body').on('change' , '#group',function(e){

      if(e.target.value.length){
          $('.role-div').show();
      }else{
          $('.role-div').hide();
      }

      let role =  e.target.value;
          if(role){
            $.ajax({
              "headers":{
              'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
              },
            'type':'get',
            'url' : '{{ route('user/getRolesByGroup') }}',
            'data' : {id : role},
            'success' : function(response){
              if(response.status){
                let html  = '';
                    html += '<option value="">-- select --</option>';
                  response.data.map(function(item,index){
                      html += '<option value="'+item.id+'"">'+item.name+'</option>';
                  });

               $('#role-div').show();
               $("#role").html(html);
              }else{
                swal(response.message , "danger");
              }
            },
            'error' : function(error){
               console.log('Something went wrong');
              }
            });
        }
   });

   let dynamicAuthInput = function(email){ return '<div class="item"><div class="col-md-10"><div class="form-group"><input type="email" name="high_authority_user[]" class="form-control" value="'+email+'" placeholder="Authority email" readonly></div></div><div class="col-md-2 text-right"><button class="btn btn-default removeInput">Remove</button></div></div>'; };

   let dynamicAuthInputFirst = function(email){ return '<div class="item"><div class="col-md-10"><div class="form-group"><input id="authority-email" type="email" name="high_authority_user[]" class="form-control" value="'+email+'" placeholder="Authority email"></div></div><div class="col-md-2 text-right"><button class="btn btn-default" id="addInput">Add More</button></div></div>'; };

   @if(isset($data['user']))
         @if(!empty($data['user']->high_authority_user) && !is_null($data['user']->high_authority_user))
          //  let html    = $('.high-authority-email-container').html();
              let html    = '';

              @php    $emails = json_decode($data['user']->high_authority_user) @endphp

              @foreach ($emails as $key => $email)
                         @if ( (count($emails) - 1) == $key )
                          $('.add-more-container').html(dynamicAuthInputFirst('{{$email->email}}'));
                         @else
                          html += dynamicAuthInput('{{$email->email}}');
                         @endif
              @endforeach
                    $('.high-authority-email-container').html(html);
                    
         @endif
   @endif

   $(document).ready(function(){

      $('body').on('click', '#addInput' ,function(e){
          e.preventDefault();
          let  status  = true;
          let email    = $('#authority-email').val();

          if(!email){
               alert('Please provide email address');
               status = false;
          }
          
          if(email){
            if(!validateEmail(email)){
                 alert('Invalid email address');
                 status = false;
            }
          }

          let reserveEmail =  $("input[name='high_authority_user[]']").map(function () {
                      return this.value; // $(this).val()
                  }).get();

              if(reserveEmail.length > 0){
                    reserveEmail.pop();

                    if(reserveEmail.length > 0){
                         reserveEmail.map(function(item){
                             if(item == email){
                                 alert('Authority emails must be unique');
                                 status = false;
                             }
                         });
                    }

              }


            if(status){
                let html  = $('.high-authority-email-container').html();
                    html += dynamicAuthInput(email);
                            $('.high-authority-email-container').html(html);
                    $('#authority-email').val('');
            }
      })

      $('body').on('click', '.removeInput' ,function(e){
                  e.preventDefault();
                  $(this).closest(".item").remove();
      });


 

   });

 $('body').on('click', '#submit-btn' ,function(e){

        let x =  $("input[name='high_authority_user[]']").map(function () {
                      return this.value; // $(this).val()
              }).get();

        if(x == undefined || x == '' || x == null || x.length <= 0){
           e.preventDefault();
           alert('Authority email field can not be empty');
        }else{
          if(duplicateEmailCheck()){
             $('#userForm').submit();
          }
          e.preventDefault();
        }
   });

  let duplicateEmailCheck = function(){
          let email    = $('#authority-email').val();
          let status   = true;
          if(email){
            if(!validateEmail(email)){
                 alert('Invalid email address');
                 status = false;
            }
          }

          let reserveEmail =  $("input[name='high_authority_user[]']").map(function () {
                      return this.value; // $(this).val()
              }).get();

            if(reserveEmail.length > 0){
                  reserveEmail.pop();
                  if(reserveEmail.length > 0){
                       reserveEmail.map(function(item){
                           if(item == email){
                               alert('Authority emails must be unique');
                                status = false;
                           }
                       });
                  }

            }

            return status;
   }

     function validateEmail(emailField){
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

        if (reg.test(emailField) == false) 
            return false;

        return true;

}


</script>
@endsection