@extends('layouts.app')
@section('content')   
     <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Update Profile
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        {{-- <li><a href="#">Examples</a></li> --}}
        <li class="active">Profile</li>
      </ol>
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

      <div class="col-md-10">

      <div class="row">
          
        <!-- Default box -->
        <div class="box box-solid">
          <div class="box-header">
            <h3 class="box-title">Profile</h3>
          </div>
          <form data-toggle="validator" class="form" action="{{ route('profile/update') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
           {{method_field('PUT')}}
            <div class="box-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="profile-image-container text-center">
                  <img id="image_view" src="{{ asset('public/images/profile_images/'.$data['user']->profile_image) }}" width="35%" height="100px">
                  <label for="profile_image" class="btn btn-default btn-flat btn-block" style="width: 40%;margin-left:100px;"><i style="color:#3a88bc">Choose Image</i></label>
                  <input type="file" name="profile_image" id="profile_image">
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>First Name :</label>
                      <input type="text" name="first_name" value="{{ $data['user']->first_name }}" class="form-control" placeholder="First Name" style="border-top: none;border-left: none;border-right: none;">
                      @if ($errors->has('first_name'))
                      <span class="help-block text-red">
                      <strong>{{ $errors->first('first_name') }}</strong>
                      </span>
                      @endif
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Last Name :</label>
                      <input type="text" name="last_name" value="{{ $data['user']->last_name }}" class="form-control" placeholder="Last Name" style="border-top: none;border-left: none;border-right: none;">
                      @if ($errors->has('last_name'))
                      <span class="help-block text-red">
                      <strong>{{ $errors->first('last_name') }}</strong>
                      </span>
                      @endif
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Contact Number :</label>
                      <input type="text" name="mobile" value="{{ $data['user']->mobile }}" class="form-control" placeholder="Contact Number" style="border-top: none;border-left: none;border-right: none;">
                      @if ($errors->has('mobile'))
                      <span class="help-block text-red">
                      <strong>{{ $errors->first('mobile') }}</strong>
                      </span>
                      @endif
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label>Email Address :</label>
                      <input type="text" name="email" value="{{ $data['user']->email }}" class="form-control" placeholder="Email Address" style="border-top: none;border-left: none;border-right: none;background: #ffffff;" disabled>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group">
                      <label>Gender :</label>&nbsp;&nbsp;&nbsp;&nbsp;
                      <input type="radio" name="gender" value="Male" @if ($data['user']->gender == 'Male')
                        {{ 'checked' }}
                      @endif >&nbsp;&nbsp;Male &nbsp;&nbsp;&nbsp;&nbsp;
                      <input type="radio" name="gender" value="Female" @if ($data['user']->gender == 'Female')
                        {{ 'checked' }}
                      @endif>
                      &nbsp;&nbsp;Female
                      @if ($errors->has('gender'))
                      <span class="help-block text-red">
                      <strong>{{ $errors->first('gender') }}</strong>
                      </span>
                      @endif
                    </div>
                  </div>
                  </div>
                  <div class="col-md-6">
                </div>
              </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <input type="submit" value="Update" class="btn btn-success btn-flat">
            </div>
            <!-- /.box-footer-->
          </form>
          </div>
          </div>
            <div class="col-md-12 text-right" style="margin-bottom: 10px;">
          <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
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
