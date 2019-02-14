@extends('layouts.app')
@section('content')   
     <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        User Profile
        <small>it all starts here</small>
      </h1>
       {{ Breadcrumbs::render('user') }}
    </section>

    <!-- Main content -->
    <section class="content">

    <div class="container emp-profile">
                <div class="row">
                    <div class="col-md-4">
                        <div class="profile-img">
                            <img src="{{ asset('public/images/profile_images/'.$data['user']->profile_image) }}"  alt="" style="width:35%;height:100px" />
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="profile-head">
                                    <h2>
                                        {{ucwords($data['user']->first_name.' '.$data['user']->last_name)}}
                                    </h2>
                                    <h3 style="margin-top: -10px; color:gray">
                                        {{ucwords($data['user']->role)}}
                                    </h3>
                                    <p class="proile-rating">Status : @switch($data['user']->status)
                                        @case(1)
                                          <span class="text-green">Active</span>
                                        @break
                                        @default
                                          <span class="text-red">Inactive</span>
                                    @endswitch
                                    </p>
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">About</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <a href="{{route('user/edit' , 'id='.$data['user']->id)}}" class="btn btn-default btn-rounded">Edit Profile</a>
                        <a href="{{URL::previous()}}" class="btn btn-default btn-back">Back</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-8">
                        <div class="tab-content profile-tab" id="myTabContent">
                            <div class="tab-pane show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>User Id</label>
                                  </div>
                                  <div class="col-md-6">
                                      <p>{{$data['user']->id}}</p>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Name</label>
                                  </div>
                                  <div class="col-md-6">
                                      <p>{{ucwords($data['user']->first_name.' '.$data['user']->last_name)}}</p>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Email</label>
                                  </div>
                                  <div class="col-md-6">
                                      <p>{{$data['user']->email}}</p>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Phone</label>
                                  </div>
                                  <div class="col-md-6">
                                      <p>{{$data['user']->mobile}}</p>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Role Group</label>
                                  </div>
                                  <div class="col-md-6">
                                     @if(is_null($data['user']->group_deleted_at))
                                      <p>{{ ucwords($data['user']->group_name) }}</p>
                                     @else
                                      <p>N/A</p>
                                     @endif
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Role</label>
                                  </div>
                                  <div class="col-md-6">
                                      <p>{{ ucwords($data['user']->role) }}</p>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>District</label>
                                  </div>
                                  <div class="col-md-6">
                                      <p>{{$data['user']->distric}}</p>
                                  </div>
                              </div>
                               <div class="row">
                                  <div class="col-md-6">
                                      <label>Authority Email</label>
                                  </div>
                                  <div class="col-md-6">
                                    @forelse($data['user']->high_authority_users as $user)
                                      <p>{{ ucwords($user->email ) }}</p>
                                    @empty
                                      <p>Auth user not available</p>
                                    @endforelse
                                  </div>
                              </div>
                            </div>
                        </div>
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
 </style>

 <style type="text/css">
.emp-profile{
    padding: 3%;
    margin-top: 3%;
    margin-bottom: 3%;
    border-radius: 0.5rem;
    background: #fff;
}
.profile-img{
    text-align: center;
}
.profile-img img{
    width: 70%;
    height: 168px;
}
.profile-img .file {
    position: relative;
    overflow: hidden;
    margin-top: -20%;
    width: 70%;
    border: none;
    border-radius: 0;
    font-size: 15px;
    background: #212529b8;
}
.profile-img .file input {
    position: absolute;
    opacity: 0;
    right: 0;
    top: 0;
}
.profile-head h5{
    color: #333;
}
.profile-head h6{
    color: #3a88bc;
}
.btn-rounded{
    border: none;
    border-radius: 1.5rem;
    width: 70%;
    padding: 2%;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
    display: inline;
}

.btn-back{
    border: none;
    border-radius: 1.5rem;
    width: 80px;
    padding: 2%;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
}

.proile-rating{
    font-size: 12px;
    color: #818182;
    margin-top: 5%;
}
.proile-rating span{
    color: #495057;
    font-size: 15px;
    font-weight: 600;
}
.profile-head .nav-tabs{
    margin-bottom:5%;
}
.profile-head .nav-tabs .nav-link{
    font-weight:600;
    border: none;
}
.profile-head .nav-tabs .nav-link.active{
    border: none;
    border-bottom:2px solid #3a88bc;
}
.profile-work{
    padding: 14%;
    margin-top: -15%;
}
.profile-work p{
    font-size: 12px;
    color: #818182;
    font-weight: 600;
    margin-top: 10%;
}
.profile-work a{
    text-decoration: none;
    color: #495057;
    font-weight: 600;
    font-size: 14px;
}
.profile-work ul{
    list-style: none;
}
.profile-tab label{
    font-weight: 600;
}
.profile-tab p{
    font-weight: 600;
    color: #3a88bc;
}
 </style>
@endsection
@section('js-script')
<!-- bootstrap validator js -->
<script src="{{ asset('public/bootstrap/js/bootstrapValidator.min.js')}}"></script>
 <script type="text/javascript">
   $('#profile_image').change(function (event) {
       var tmppath = URL.createObjectURL(event.target.files[0]);
       $('#image_view').attr('src',tmppath);
   });
</script>
@endsection


